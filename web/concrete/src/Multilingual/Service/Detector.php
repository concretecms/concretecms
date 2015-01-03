<?php

namespace Concrete\Core\Multilingual\Service;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Page;
use Session;
use Cookie;
use Config;

defined('C5_EXECUTE') or die("Access Denied.");

class Detector
{

    /**
     *
     * Returns the preferred locale based on session, cookie,
     * user object, default browser (if allowed), and finally
     * site preferences. Returns a string, not a section.
     * Since the user's language is not a locale but a language,
     * attempts to determine best locale for the given language.
     * @return Section
     */
    public static function getPreferredSection()
    {

        $locale = false;
        // they have a language in a certain session going already
        if (Session::has('multilingual_default_locale')) {
            $locale = Session::get('multilingual_default_locale');
        } else if (Cookie::has('multilingual_default_locale')) {
            $locale = Cookie::get('multilingual_default_locale');
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

        if (Config::get('concrete.multilingual.use_browser_detected_language')) {
            $home = false;
            $locales =  \Punic\Misc::getBrowserLocales();
            foreach($locales as $locale => $value) {
                $home = Section::getByLocaleOrLanguage($value);
                if ($home) {
                    break;
                }
            }

            if ($home) {
                return $home;
            }
        }

        return Section::getByLocale(Config::get('concrete.multilingual.default_locale'));
    }

    public static function setupSiteInterfaceLocalization(Page $c = null)
    {
        if (!$c) {
            $c = Page::getCurrentPage();
        }
        // don't translate dashboard pages
        $dh = \Core::make('helper/concrete/dashboard');
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

        if (strlen($locale)) {
            \Localization::changeLocale($locale);
        }
    }
}
