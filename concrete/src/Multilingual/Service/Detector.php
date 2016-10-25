<?php
namespace Concrete\Core\Multilingual\Service;

use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Facade;

defined('C5_EXECUTE') or die("Access Denied.");

class Detector
{
    /**
     * Returns the preferred section based on session, cookie,
     * user object, default browser (if allowed), and finally
     * site preferences.
     * Since the user's language is not a locale but a language,
     * attempts to determine best section for the given language.
     *
     * @return Section
     */
    public static function getPreferredSection()
    {

        $site = \Site::getSite();

        $locale = false;
        $app = Facade::getFacadeApplication();
        // they have a language in a certain session going already
        $session = $app->make('session');
        if ($session->has('multilingual_default_locale')) {
            $locale = $session->get('multilingual_default_locale');
        } else {
            $cookie = $app->make('cookie');
            if ($cookie->has('multilingual_default_locale')) {
                $locale = $cookie->get('multilingual_default_locale');
            }
        }

        if ($locale) {
            $home = Section::getByLocale($locale);
            if ($home) {
                return $home;
            }
        }

        $u = new \User();
        if ($u->isRegistered()) {
            $userDefaultLanguage = $u->getUserDefaultLanguage();
            if ($userDefaultLanguage) {
                $home = Section::getByLocaleOrLanguage($userDefaultLanguage);
                if ($home) {
                    return $home;
                }
            }
        }

        $config = $site->getConfigRepository();
        if ($config->get('multilingual.use_browser_detected_locale')) {
            $home = false;
            $locales = \Punic\Misc::getBrowserLocales();
            foreach (array_keys($locales) as $locale) {
                $home = Section::getByLocaleOrLanguage($locale);
                if ($home) {
                    break;
                }
            }

            if ($home) {
                return $home;
            }
        }

        $site = \Site::getSite();
        $config = $site->getConfigRepository();
        return Section::getByLocale($config->get('multilingual.default_locale'));
    }

    public static function setupSiteInterfaceLocalization(Page $c = null)
    {
        if (!$c) {
            $c = Page::getCurrentPage();
        }
        $app = Facade::getFacadeApplication();
        // don't translate dashboard pages
        $dh = $app->make('helper/concrete/dashboard');
        if ($dh->inDashboard($c)) {
            return;
        }

        $ms = Section::getBySectionOfSite($c);
        if (!is_object($ms)) {
            $ms = static::getPreferredSection();
        }

        if (!$ms) {
            return;
        }

        $locale = $ms->getLocale();
        if ($locale) {
            $app->make('session')->set('multilingual_default_locale', $locale);
            $loc = Localization::getInstance();
            $loc->setContextLocale('site', $locale);
        }
    }

    /**
     * Check if there's some multilingual section.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        $app = Facade::getFacadeApplication();
        $cache = $app->make('cache/request');
        $item = $cache->getItem('multilingual/enabled');
        if (!$item->isMiss()) {
            return $item->get();
        }

        $item->lock();
        $result = false;
        if ($app->isInstalled()) {
            $db = $app->make('database')->connection();
            if ($db->executeQuery('select cID from MultilingualSections limit 1')->fetchColumn()) {
                $result = true;
            }
        }

        $cache->save($item->set($result));
        return $result;
    }
}
