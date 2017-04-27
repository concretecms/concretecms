<?php
namespace Concrete\Core\Localization;

use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader as ZendSiteTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapter as ZendTranslatorAdapter;
use Concrete\Core\Localization\Translator\TranslatorAdapterRepositoryInterface;
use Concrete\Core\Support\Facade\Facade;
use Core;
use Exception;
use Punic\Data as PunicData;
use Zend\I18n\Translator\Translator as ZendTranslator;

class Localization
{
    /**
     * The "base" locale identifier.
     *
     * @var string
     */
    const BASE_LOCALE = 'en_US';

    /**
     * The context (resolving to en_US) to be considered as the "neutral" one.
     *
     * This context must be used for all strings that are system related in concrete5 and should have their own context.
     * Generally, these are the strings that concrete5 saves in the database, such as package, theme and block type names/descriptions.
     *
     * @var string
     */
    const CONTEXT_SYSTEM = 'system';

    /**
     * This is the context for the site interface tranlations.
     * It contains the page locale, determined by the page-specific locale or by the site section it's contained in.
     * When there's no locale associated to the current page, this context is associated to the locale specified in the concrete.locale configuration option.
     *
     * These are all the translations that the site visitors see on the site.
     * The editor also sees these strings in the same language as the visitor.
     *
     * @var string
     */
    const CONTEXT_SITE = 'site';

    /**
     * The context containing the locale of the current user (fallsback to the concrete.locale configuration option).
     * This should be the context used when showing the edit dialogs, the concrete5 menus...
     *
     * @var string
     */
    const CONTEXT_UI = 'ui';

    /**
     * The translator adapter repository to be used.
     *
     * @var TranslatorAdapterRepositoryInterface
     */
    protected $translatorAdapterRepository;

    /**
     * The locale identifier to be used for every translation context.
     *
     * @var array
     */
    protected $contextLocales = [];

    /**
     * The currently active translation context.
     *
     * @var string|null
     */
    protected $activeContext = null;

    /**
     * Tracks the list of active contexts.
     *
     * @var array
     *
     * @see Localization::pushActiveContext()
     * @see Localization::popActiveContext()
     */
    protected $activeContextQueue = [];

    /**
     * Gets the translator adapter repository.
     *
     * @return TranslatorAdapterRepositoryInterface
     *
     * @throws Exception in case the translator adapter repository has not been
     *                   set, an exception is thrown
     */
    public function getTranslatorAdapterRepository()
    {
        if (!isset($this->translatorAdapterRepository)) {
            // Note: Do NOT call the t() function here as it would cause an
            // infinte loop if the translator adapter repository has not been
            // set.
            throw new Exception('Translator adapter repository has not been set.');
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
        $oldLocale = isset($this->activeContext) ? $this->contextLocales[$this->activeContext] : null;
        $this->activeContext = $context;
        if (!isset($this->contextLocales[$context])) {
            $this->setContextLocale($context, static::BASE_LOCALE);
        }
        $newLocale = $this->contextLocales[$context];
        if ($newLocale !== $oldLocale) {
            $this->currentLocaleChanged($newLocale);
        }
    }

    /**
     * Change the active translation context, but remember the previous one.
     * Useful when temporarily setting the translation context to something else than the original.
     *
     * @param string $newContext The new translation context to activate (default contexts are defined by the Localization::CONTEXT_... constants).
     *
     * @see Localization::popActiveContext()
     *
     * @example
     * ```php
     * $loc = \Localization::getInstance();
     * // Let's assume the current context is 'original_context'
     *
     * $loc->pushActiveContext('new_context');
     *
     * // Do what you want in context 'new_context'
     *
     * $loc->popActiveContext();
     * // Now the context is 'original_context'
     * ```
     */
    public function pushActiveContext($newContext)
    {
        if ($this->activeContext !== null) {
            $this->activeContextQueue[] = $this->activeContext;
        }
        $this->setActiveContext($newContext);
    }

    /**
     * Restore the context that was active before calling pushActiveContext.
     *
     * @see Localization::pushActiveContext()
     */
    public function popActiveContext()
    {
        if (!empty($this->activeContextQueue)) {
            $oldContext = array_pop($this->activeContextQueue);
            $this->setActiveContext($oldContext);
        }
    }

    /**
     * Gets the translator adapter object for the given context from the
     * translator adapter repository.
     *
     * @return \Concrete\Core\Localization\Translator\TranslatorAdapterInterface
     *
     * @throws Exception in case trying to fetch an adapter for an unknown
     *                   context, an exception is thrown
     */
    public function getTranslatorAdapter($context)
    {
        if (!isset($this->contextLocales[$context])) {
            // Note: Do NOT call the t() function here as it might possibly
            // cause an infinte loop in case this happens with the active
            // context.
            throw new Exception(sprintf('Context locale has not been set for context: %s', $context));
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
        if ($context === $this->activeContext) {
            $this->currentLocaleChanged($locale);
        }
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
        $languages = [];
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
        $locales = [];
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
        $app->make('cache/expensive')->getItem('zend')->clear();

        // Also remove the loaded translation adapters so that old strings are
        // not being used from the adapters already in memory.
        $loc = static::getInstance();
        $loc->removeLoadedTranslatorAdapters();
    }

    /**
     * Load the site language files (must be done after all packages called their setupPackageLocalization).
     *
     * @deprecated use \Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader instead
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

    /**
     * To be called every time the current locale changes.
     *
     * @param string $locale
     */
    protected function currentLocaleChanged($locale)
    {
        PunicData::setDefaultLocale($locale);
        $app = Facade::getFacadeApplication();
        if ($app->bound('director')) {
            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument('locale', $locale);
            $app->make('director')->dispatch('on_locale_load', $event);
        }
    }
}
