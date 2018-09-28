<?php

namespace Concrete\Core\Multilingual\Service;

use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die('Access Denied.');

class Detector
{
    /**
     * Returns the preferred section based on session, cookie,
     * user object, default browser (if allowed), and finally
     * site preferences.
     * Since the user's language is not a locale but a language,
     * attempts to determine best section for the given language.
     *
     * @return Section|null
     */
    public static function getPreferredSection()
    {
        $app = Facade::getFacadeApplication();
        $site = $app->make('site')->getSite();
        $siteConfig = $site->getConfigRepository();
        $session = $app->make('session');

        $result = null;
        if ($result === null) {
            $locale = false;
            // Detect locale by value stored in session or cookie
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
                    $result = [$locale, $home];
                }
            }
        }

        if ($result === null) {
            // Detect locale by user's preferred language
            $u = new \User();
            if ($u->isRegistered()) {
                $userDefaultLanguage = $u->getUserDefaultLanguage();
                if ($userDefaultLanguage) {
                    $home = Section::getByLocaleOrLanguage($userDefaultLanguage);
                    if ($home) {
                        $result = [$userDefaultLanguage, $home];
                    }
                }
            }
        }

        if ($result === null) {
            // Detect locale by browsers headers
            if ($siteConfig->get('multilingual.use_browser_detected_locale')) {
                $home = false;
                $browserLocales = \Punic\Misc::getBrowserLocales();
                foreach (array_keys($browserLocales) as $browserLocale) {
                    $home = Section::getByLocaleOrLanguage($browserLocale);
                    if ($home) {
                        $result = [$home->getLocale(), $home];
                        break;
                    }
                }
            }
        }

        if ($result === null) {
            // Use the default site locale
            $locale = $site->getDefaultLocale();
            $home = Section::getByLocale($locale);
            if ($home) {
                $result = [$locale, $home];
            }
        }

        if ($result !== null) {
            if ($siteConfig->get('multilingual.always_track_user_locale')) {
                $storeLocale = true;
            } else {
                $sessionValidator = $app->make(SessionValidator::class);
                $storeLocale = $sessionValidator->hasActiveSession();
            }
            if ($storeLocale) {
                $session->set('multilingual_default_locale', $result[0]);
            }
        }

        return ($result === null) ? null : $result[1];
    }

    /**
     * Set the locale associated to the 'site' localization context.
     *
     * @param Page $c The page to be used to determine the site locale (if null we'll use the current page)
     */
    public function setupSiteInterfaceLocalization(Page $c = null)
    {
        $app = Facade::getFacadeApplication();
        $loc = $app->make(Localization::class);
        $locale = null;
        if ($c === null) {
            $c = Page::getCurrentPage();
        }
        if ($c) {
            $pageController = $c->getPageController();
            if (is_callable([$pageController, 'useUserLocale'])) {
                $useUserLocale = $pageController->useUserLocale();
            } else {
                $dh = $app->make('helper/concrete/dashboard');
                $useUserLocale = $dh->inDashboard($c);
            }
            if ($useUserLocale) {
                $u = new User();
                $locale = $u->getUserLanguageToDisplay();
            } else {
                if ($this->isEnabled()) {
                    $ms = Section::getBySectionOfSite($c);
                    if (!$ms) {
                        $ms = static::getPreferredSection();
                    }
                    if ($ms) {
                        $site = $ms->getSite();
                        $siteConfig = $site->getConfigRepository();
                        $locale = $ms->getLocale();

                        if ($siteConfig->get('multilingual.always_track_user_locale')) {
                            $storeLocale = true;
                        } else {
                            $sessionValidator = $app->make(SessionValidator::class);
                            $storeLocale = $sessionValidator->hasActiveSession();
                        }
                        if ($storeLocale) {
                            $app->make('session')->set('multilingual_default_locale', $locale);
                        }
                    }
                }
                if (!$locale) {
                    $siteTree = $c->getSiteTreeObject();
                    if ($siteTree) {
                        $locale = $siteTree->getLocale()->getLocale();
                    }
                }
            }
        }
        if (!$locale) {
            $locale = $app->make('config')->get('concrete.locale');
        }
        $loc->setContextLocale(Localization::CONTEXT_SITE, $locale);
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
            $site = $app->make('site')->getSite();
            if (count($site->getLocales()) > 1) {
                $result = true;
            }
        }

        $cache->save($item->set($result));

        return $result;
    }
}
