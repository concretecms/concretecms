<?php
namespace Concrete\Core\Localization\Translator\Adapter\Laminas;

use Concrete\Core\Cache\Adapter\LaminasCacheDriver;
use Concrete\Core\Localization\Translator\Translation\TranslationLoaderRepositoryInterface;
use Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\LoaderPluginManager;

/**
 * Provides a factory method to create translator objects for the Laminas
 * translator.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterFactory implements TranslatorAdapterFactoryInterface
{
    /** @var TranslationLoaderRepositoryInterface */
    protected $translationLoaderRepository;

    /**
     * @var \Laminas\I18n\Translator\LoaderPluginManager|null
     */
    protected $loaderPluginManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(TranslationLoaderRepositoryInterface $translationLoaderRepository = null, LoaderPluginManager $loaderPluginManager = null)
    {
        $this->translationLoaderRepository = $translationLoaderRepository;
        $this->loaderPluginManager = $loaderPluginManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslatorAdapter($locale)
    {
        $cache = new LaminasCacheDriver('cache/expensive');

        $t = new Translator();
        if ($this->loaderPluginManager !== null) {
            $t->setPluginManager($this->loaderPluginManager);
        }
        $t->setCache($cache);

        $adapter = new TranslatorAdapter($t);
        $adapter->setLocale($locale);

        if (isset($this->translationLoaderRepository)) {
            foreach ($this->translationLoaderRepository->getTranslationLoaders() as $key => $loader) {
                $loader->loadTranslations($adapter);
            }
        }

        return $adapter;
    }
}
