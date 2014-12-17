<?php
namespace Concrete\Core\Localization;
use Config;
use Concrete\Core\Cache\Adapter\ZendCacheDriver;
use Loader;
use Events;
use \Zend\I18n\Translator\Translator;
use \Punic\Data as PunicData;

class Localization
{
    private static $loc = null;
    /**
     * @var ZendCacheDriver|null
     */
    private static $cache = null;

    public static function getInstance()
    {
        if (null === self::$loc) {
            self::$loc = new self();

        }

        return self::$loc;
    }

    public static function changeLocale($locale)
    {
        $loc = Localization::getInstance();
        $loc->setLocale($locale);
    }
    /** Returns the currently active locale
     * @return string
     * @example 'en_US'
     */
    public static function activeLocale()
    {
        $loc = Localization::getInstance();

        return $loc->getLocale();
    }
    /** Returns the language for the currently active locale
     * @return string
     * @example 'en'
     */
    public static function activeLanguage()
    {
        return current(explode('_', self::activeLocale()));
    }

    protected $translate;

    public function setLocale($locale)
    {
        $localeNeededLoading = false;
        if (($locale == 'en_US') && (!Config::get('concrete.misc.enable_translate_locale_en_us'))) {
            if (isset($this->translate)) {
                unset($this->translate);
            }
            PunicData::setDefaultLocale($locale);
            return;
        }
        if (is_dir(DIR_LANGUAGES . '/' . $locale)) {
            $languageDir = DIR_LANGUAGES . '/' . $locale;
        } elseif (is_dir(DIR_LANGUAGES_CORE . '/' . $locale)) {
            $languageDir = DIR_LANGUAGES_CORE . '/' . $locale;
        } else {
            return;
        }

        $this->translate = new Translator();
        $this->translate->addTranslationFilePattern('gettext', $languageDir, 'LC_MESSAGES/messages.mo');
        $this->translate->setLocale($locale);
        $this->translate->setCache(self::getCache());
        PunicData::setDefaultLocale($locale);

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('locale', $locale);
        Events::dispatch('on_locale_load', $event);
    }

    public function getLocale()
    {
        return isset($this->translate) ? $this->translate->getLocale() : 'en_US';
    }

    public function getActiveTranslateObject()
    {
        return $this->translate;
    }

    public function addSiteInterfaceLanguage($language)
    {
        if (!is_object($this->translate)) {
            $this->translate = new Translator();
            $this->translate->setCache(self::getCache());
        }
        $this->translate->addTranslationFilePattern('gettext', DIR_LANGUAGES_SITE_INTERFACE, $language . '.mo');
    }

    public static function getTranslate()
    {
        $loc = Localization::getInstance();

        return $loc->getActiveTranslateObject();
    }

    public static function getAvailableInterfaceLanguages()
    {
        $languages = array();
        $fh = Loader::helper('file');

        if (file_exists(DIR_LANGUAGES)) {
            $contents = $fh->getDirectoryContents(DIR_LANGUAGES);
            foreach ($contents as $con) {
                if (is_dir(DIR_LANGUAGES . '/' . $con) && file_exists(DIR_LANGUAGES . '/' . $con . '/LC_MESSAGES/messages.mo')) {
                    $languages[] = $con;
                }
            }
        }
        if (file_exists(DIR_LANGUAGES_CORE)) {
            $contents = $fh->getDirectoryContents(DIR_LANGUAGES_CORE);
            foreach ($contents as $con) {
                if (is_dir(DIR_LANGUAGES_CORE . '/' . $con) && file_exists(DIR_LANGUAGES_CORE . '/' . $con . '/LC_MESSAGES/messages.mo') && (!in_array($con, $languages))) {
                    $languages[] = $con;
                }
            }
        }

        return $languages;
    }

    /**
     * Generates a list of all available languages and returns an array like
     * [ "de_DE" => "Deutsch (Deutschland)",
     *   "en_US" => "English (United States)",
     *   "fr_FR" => "Francais (France)"]
     * The result will be sorted by the key.
     * If the $displayLocale is set, the language- and region-names will be returned in that language
     * @param string|null $displayLocale Language of the description.
     *                    Set to null to get each locale name in its own language,
     *                    set to '' to use the current locale,
     *                    set to a specific locale to get the names in that language
     * @return Array An associative Array with locale as the key and description as content
     */
    public static function getAvailableInterfaceLanguageDescriptions($displayLocale = '')
    {
        $languages = self::getAvailableInterfaceLanguages();
        if (count($languages) > 0) {
            array_unshift($languages, 'en_US');
        }
        $locales = array();
        foreach ($languages as $lang) {
            $locales[$lang] = self::getLanguageDescription($lang, $displayLocale);
        }
        natcasesort($locales);

        return $locales;
    }

    /**
     * Get the description of a locale consisting of language and region description
     * e.g. "French (France)"
     * @param string $locale Locale that should be described
     * @param string|null $displayLocale Language of the description.
     *                    Set to null to get each locale name in its own language,
     *                    set to '' to use the current locale,
     *                    set to a specific locale to get the names in that language
     * @return string Description of a language
     */
    public static function getLanguageDescription($locale, $displayLocale = '')
    {
        return \Punic\Language::getName($locale, is_null($displayLocale) ? $locale : $displayLocale);
    }

    /**
     * @return ZendCacheDriver
     */
    protected static function getCache()
    {
        if (is_null(self::$cache)) {
            self::$cache = new ZendCacheDriver('cache/expensive');
        }

        return self::$cache;
    }

    /**
     * Clear the translations cache
     */
    public static function clearCache()
    {
        self::getCache()->flush();
    }
}
