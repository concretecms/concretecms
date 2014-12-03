<?php

namespace Concrete\Core\Multilingual\Service;

defined('C5_EXECUTE') or die("Access Denied.");

class DefaultLanguage
{

    public function checkDefaultLanguage()
    {
        $req = Request::get();
        if (!$_SERVER['REQUEST_METHOD'] != 'POST') {
            if (!$req->getRequestCollectionPath() && $req->getRequestCollectionID() == 1 && (!$req->isIncludeRequest())
            ) {
                $p = $req->getCurrentPage();
                if (is_object($p) && (!$p->isError())) {
                    $pkg = Package::getByHandle('multilingual');
                    if ($pkg->config('REDIRECT_HOME_TO_DEFAULT_LANGUAGE')) {
                        $ms = MultilingualSection::getByLocale(DefaultLanguageHelper::getSessionDefaultLocale());
                        if (is_object($ms)) {
                            if ($ms->getCollectionID() != 1) {
                                header('Location: ' . Loader::helper('navigation')->getLinkToCollection($ms, true));
                                exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /*
     * To do the redirect to the browser language:
     *  - check the package's controller.php: it extends on_start to use checkdefaltlanguage() above.
     *       So this wil add a config option of "Default language is determined by User's browser." or "Detect default language from visitor's browser"
     *       Instead of adding another function to extend the on_start event, just add another check in the checkdefaultlanguage for the new config variable
     *       - get the browser's language with the zend library
     *       - see if there is a language for the browser
     *       - if yes, use that, otherwise use the default
     */

    // first checks to see if there is a cookie set with this
    // otherwise we retrieve it from the sitewide multilingual settings

    public function getSessionDefaultLocale()
    {
        // they have a language in a certain session going already
        if (isset($_SESSION['DEFAULT_LOCALE'])) {
            return $_SESSION['DEFAULT_LOCALE'];
        }

        // if they've specified their own default locale to remember
        if (isset($_COOKIE['DEFAULT_LOCALE'])) {
            return $_COOKIE['DEFAULT_LOCALE'];
        }

        if (User::isLoggedIn()) {
            $u = new User();
            $userDefaultLanguage = $u->getUserDefaultLanguage();
            if ($userDefaultLanguage != '') {
                if (is_object(
                        MultilingualSection::getByLocale($userDefaultLanguage)
                    ) || ($userDefaultLanguage == 'en_US' && Page::getCurrentPage()->cID != 1)
                ) {
                    return $userDefaultLanguage;
                }
            }
        }

        $pkg = Package::getByHandle('multilingual');
        //
        // This could set the cookie here but it does not.
        // If something wants to set the default language it should probably do that outside of here.
        //
        if ($pkg->config('TRY_BROWSER_LANGUAGE')) {
            Loader::model('section', 'multilingual');
            Loader::library('3rdparty/Zend/Locale');
            $locale = new Zend_Locale();


            if (is_object(MultilingualSection::getByLocale((string)$locale))) {
                return (string)$locale;
            } else {
                $section = MultilingualSection::getByLanguage((string)$locale->getLanguage());
                if (is_object($section)) {
                    return (string)$section->getLocale();
                }
            }
        }

        return $pkg->config('DEFAULT_LANGUAGE');
    }

    public static function setupSiteInterfaceLocalization()
    {
        // don't translate dashboard pages
        $c = Page::getCurrentPage();
        if ($c instanceof Page && Loader::helper('section', 'multilingual')->section('dashboard')) {
            return;
        }

        $ms = MultilingualSection::getCurrentSection();
        if (is_object($ms)) {
            $locale = $ms->getLocale();
        } else {
            $locale = DefaultLanguageHelper::getSessionDefaultLocale();
        }

        // change core language to translate e.g. core blocks/themes
        if (strlen($locale)) {
            Localization::changeLocale($locale);
        }

        // site translations
        if (is_dir(DIR_LANGUAGES_SITE_INTERFACE)) {
            if (file_exists(DIR_LANGUAGES_SITE_INTERFACE . '/' . $locale . '.mo')) {
                $loc = Localization::getInstance();
                $loc->addSiteInterfaceLanguage($locale);
            }
        }

        // add package translations
        if (strlen($locale)) {
            $ms = MultilingualSection::getByLocale($locale);
            if ($ms instanceof MultilingualSection) {
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
