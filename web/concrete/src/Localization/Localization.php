<?php
namespace Concrete\Core\Localization;

use Config;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader as ZendSiteTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapter as ZendTranslatorAdapter;
use Concrete\Core\Localization\Translator\TranslatorAdapterRepositoryInterface;
use Core;
use Events;
use Exception;
use Punic\Data as PunicData;
use Zend\I18n\Translator\Translator as ZendTranslator;

class Localization
{

    /** @var string */
    const BASE_LOCALE = 'en_US';

    /** @var TranslatorAdapterRepositoryInterface */
    protected $translatorAdapterRepository;

    /** @var array */
    protected $contextLocales = array();
    /** @var string|null */
    protected $activeContext = null;
    /** @var array */
    protected $activeContextQueue = array();

    /**
     * Gets the translator adapter repository.
     *
     * @return TranslatorAdapterRepositoryInterface
     *
     * @throws Exception In case the translator adapter repository has not been
     *                   set, an exception is thrown.
     */
    public function getTranslatorAdapterRepository()
    {
        if (!isset($this->translatorAdapterRepository)) {
            // Note: Do NOT call the t() function here as it would cause an
            // infinte loop if the translator adapter repository has not been
            // set.
            throw new Exception("Translator adapter repository has not been set.");
        }
        return $this->translatorAdapterRepository;
    }

    /**
     * Sets the translator adapter repository.
     *
     * @param TranslatorAdapterRepositoryInterface $repository
     */
    public function setTranslatorAdapterRepository(TranslatorAdapterRepositoryInterface $repository)
    {
        $this->translatorAdapterRepository = $repository;
    }

    /**
     * Returns the currently active translation context.
     *
     * @return string
     */
    public function getActiveContext()
    {
        return $this->activeContext;
    }

    /**
     * Sets the active translation context.
     *
     * @param string $context
     */
    public function setActiveContext($context)
    {
        if (
            $this->activeContext !== null ||
            count($this->activeContextQueue) > 0
        ) {
            $this->activeContextQueue[] = $this->activeContext;
        }
        $this->activeContext = $context;

        if (!isset($this->contextLocales[$context])) {
            $this->setContextLocale($context, static::BASE_LOCALE);
        }
    }

    /**
     * Reverts the active translation context to the previous context. Useful
     * when temporarily setting the translation context to something else than
     * the original.
     *
     * Usage:
     *
     * ```php
     * $loc = \Localization::getInstance();
     * $loc->setActiveContext('first_context');
     *
     * // Do something else
     * $loc->setActiveContext('second_context');
     * $foo->bar();
     *
     * // Revert the context back
     * $loc->revertActiveContext();
     *
     * echo $loc->getActiveContext(); // Prints out 'first_context'
     * ```
     */
    public function revertActiveContext()
    {
        $this->activeContext = array_pop($this->activeContextQueue);
    }

    /**
     * Gets the translator adapter object for the given context from the
     * translator adapter repository.
     *
     * @return \Concrete\Core\Localization\Translator\TranslatorAdapterInterface
     *
     * @throws Exception In case trying to fetch an adapter for an unknown
     *                   context, an exception is thrown.
     */
    public function getTranslatorAdapter($context)
    {
        if (!isset($this->contextLocales[$context])) {
            // Note: Do NOT call the t() function here as it might possibly
            // cause an infinte loop in case this happens with the active
            // context.
            throw new Exception(sprintf("Context locale has not been set for context: %s", $context));
        }
        $locale = $this->contextLocales[$context];

        return $this->getTranslatorAdapterRepository()->getTranslatorAdapter($context, $locale);
    }

    /**
     * Gets the translator adapter for the active context.
     *
     * @return \Concrete\Core\Localization\Translator\TranslatorAdapterInterface
     */
    public function getActiveTranslatorAdapter()
    {
        return $this->getTranslatorAdapter($this->getActiveContext());
    }

    /**
     * Sets the context locale for the given context as the given locale.
     *
     * @param string $context
     * @param string $locale
     */
    public function setContextLocale($context, $locale)
    {
        if (isset($this->contextLocales[$context]) && $this->contextLocales[$context] == $locale) {
            return;
        }
        $this->contextLocales[$context] = $locale;
        //$this->getTranslatorAdapterRepository()->removeTranslatorAdaptersWithHandle($context);
    }

    /**
     * Gets the context locale for the given context.
     *
     * @return string|null
     */
    public function getContextLocale($context)
    {
        return isset($this->contextLocales[$context]) ? $this->contextLocales[$context] : null;
    }

    /**
     * Sets the locale for the active context.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->setContextLocale($this->getActiveContext(), $locale);

        PunicData::setDefaultLocale($locale);
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('locale', $locale);
        Events::dispatch('on_locale_load', $event);
    }

    /**
     * Gets the locale for the active context.
     *
     * @param string
     */
    public function getLocale()
    {
        $adapter = $this->getActiveTranslatorAdapter();

        return $adapter ? $adapter->getLocale() : static::BASE_LOCALE;
    }

    /**
     * Removes all the loaded translator adapters from the translator adapter
     * repository.
     */
    public function removeLoadedTranslatorAdapters()
    {
        foreach ($this->contextLocales as $context => $locale) {
            $this->getTranslatorAdapterRepository()->removeTranslatorAdaptersWithHandle($context);
        }
    }

    /**
     * Gets the translator object for the active context.
     *
     * @deprecated Use translator adapters instead
     *
     * @return ZendTranslator|null
     */
    public function getActiveTranslateObject()
    {
        $adapter = $this->getTranslatorAdapter($this->getActiveContext());
        if (is_object($adapter)) {
            return $adapter->getTranslator();
        }
        return null;
    }

    /**
     * Gets a singleton instance of this class.
     *
     * @return Localization
     */
    public static function getInstance()
    {
        $app = Facade::getFacadeApplication();
        return $app->make('Concrete\Core\Localization\Localization');
    }

    /**
     * Gets the translator object for the active context from from the
     * singleton instance of this class.
     *
     * @deprecated Use translator adapters instead
     *
     * @return ZendTranslator
     */
    public static function getTranslate()
    {
        $loc = static::getInstance();

        return $loc->getActiveTranslateObject();
    }

    /**
     * Sets the locale for the active context for the singleton instance of
     * this class.
     *
     * @param string $locale
     */
    public static function changeLocale($locale)
    {
        $loc = static::getInstance();
        $loc->setLocale($locale);
    }

    /**
     * Returns the currently active locale for the currently active context
     * from the singleton instance of this class.
     *
     * @return string
     *
     * @example 'en_US'
     */
    public static function activeLocale()
    {
        $loc = static::getInstance();

        return $loc->getLocale();
    }

    /**
     * Returns the language for the currently active locale for the currently
     * active context from the singleton instance of this class.
     *
     * @return string
     *
     * @example 'en'
     */
    public static function activeLanguage()
    {
        return current(explode('_', self::activeLocale()));
    }

    /**
     * Gets a list of the available site interface languages. Returns an array
     * that where each item is a locale in format xx_XX.
     *
     * @return array
     */
    public static function getAvailableInterfaceLanguages()
    {
        $languages = array();
        $fh = Core::make('helper/file');

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
     * If the $displayLocale is set, the language- and region-names will be returned in that language.
     *
     * @param string|null $displayLocale Language of the description.
     *                    Set to null to get each locale name in its own language,
     *                    set to '' to use the current locale,
     *                    set to a specific locale to get the names in that language
     *
     * @return array An associative Array with locale as the key and description as content
     */
    public static function getAvailableInterfaceLanguageDescriptions($displayLocale = '')
    {
        $languages = self::getAvailableInterfaceLanguages();
        if (count($languages) > 0) {
            array_unshift($languages, static::BASE_LOCALE);
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
     * e.g. "French (France)".
     *
     * @param string $locale Locale that should be described
     * @param string|null $displayLocale Language of the description.
     *                    Set to null to get each locale name in its own language,
     *                    set to '' to use the current locale,
     *                    set to a specific locale to get the names in that language
     *
     * @return string Description of a language
     */
    public static function getLanguageDescription($locale, $displayLocale = '')
    {
        return \Punic\Language::getName($locale, is_null($displayLocale) ? $locale : $displayLocale);
    }

    /**
     * Clear the translations cache.
     */
    public static function clearCache()
    {
        // cache/expensive should be used by the translator adapters.
        $app = Facade::getFacadeApplication();
        $app->make('cache/expensive')->flush();

        // Also remove the loaded translation adapters so that old strings are
        // not being used from the adapters already in memory.
        $loc = static::getInstance();
        $loc->removeLoadedTranslatorAdapters();
    }

    /**
     * Load the site language files (must be done after all packages called their setupPackageLocalization)
     *
     * @deprecated Use \Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader instead.
     *
     * @param ZendTranslator $translate
     */
    public static function setupSiteLocalization(ZendTranslator $translate = null)
    {
        $loc = static::getInstance();
        if ($translate === null) {
            $translate = $loc->getActiveTranslateObject();
        }
        if ($translate instanceof ZendTranslator) {
            $adapter = new ZendTranslatorAdapter($translate);
            $adapter->setLocale($translate->getLocale());

            $app = Facade::getFacadeApplication();
            $loader = new ZendSiteTranslationLoader($app);
            $loader->loadTranslations($adapter);
        }
    }

}
