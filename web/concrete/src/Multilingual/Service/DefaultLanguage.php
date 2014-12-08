<?php

namespace Concrete\Core\Multilingual\Service;

use Concrete\Core\Multilingual\Page\Section;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Page;
use Session;
use Cookie;
use Config;

defined('C5_EXECUTE') or die("Access Denied.");

class DefaultLanguage
{

    public function getSessionDefaultLocale(Page $c = null)
    {
        if (!$c) {
            $c = Page::getCurrentPage();
        }
        // they have a language in a certain session going already
        if (Session::has('multilingual.default_locale')) {
            return Session::get('multilingual.default_locale');
        }

        // if they've specified their own default locale to remember
        if (Cookie::has('multilingual.default_locale')) {
            return Cookie::get('multilingual.default_locale');
        }

        $u = new \User();
        if ($u->isRegistered()) {
            $userDefaultLanguage = $u->getUserDefaultLanguage();
            if ($userDefaultLanguage != '') {
                if (is_object(
                        Section::getByLocale($userDefaultLanguage)
                    ) || ($userDefaultLanguage == 'en_US' && is_object($c) && $c->getCollectionID() != 1)
                ) {
                    return $userDefaultLanguage;
                }
            }
        }

        if (Config::get('concrete.multilingual.use_browser_detected_language')) {
            /*$locale = 'en_US'; // need to accurately detect here.
            if (is_object(Section::getByLocale((string)$locale))) {
                return (string)$locale;
            } else {
                $section = Section::getByLanguage((string)$locale->getLanguage());
                if (is_object($section)) {
                    return (string)$section->getLocale();
                }
            }*/
        }

        return Config::get('concrete.multilingual.default_language');
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

        $ms = Section::getCurrentSection();
        if (is_object($ms)) {
            $locale = $ms->getLocale();
        } else {
            $locale = self::getSessionDefaultLocale();
        }

        // change core language to translate e.g. core blocks/themes
        if (strlen($locale)) {
            \Localization::changeLocale($locale);
        }

        // site translations
        if (is_dir(DIR_LANGUAGES_SITE_INTERFACE)) {
            if (file_exists(DIR_LANGUAGES_SITE_INTERFACE . '/' . $locale . '.mo')) {
                $loc = \Localization::getInstance();
                $loc->addSiteInterfaceLanguage($locale);
            }
        }

        // add package translations
        if (strlen($locale)) {
            $ms = Section::getByLocale($locale);
            if ($ms instanceof Section) {
                $pl = PackageList::get();
                $installed = $pl->getPackages();
                foreach ($installed as $pkg) {
                    if ($pkg instanceof Package) {
                        $pkg->setupPackageLocalization($ms->getLocale());
                    }
                }
            }
        }
    }
}
