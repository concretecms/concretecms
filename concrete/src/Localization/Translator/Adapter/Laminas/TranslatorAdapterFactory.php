<?php
namespace Concrete\Core\Localization\Translator\Adapter\Laminas;

use Concrete\Core\Cache\Adapter\LaminasCacheAdapter;
use Concrete\Core\Cache\Adapter\LaminasCacheDriver;
use Concrete\Core\Cache\Level\ExpensiveCache;
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

    /**
     * {@inheritdoc}
     */
    public function __construct(
        protected ExpensiveCache $cache,
        protected TranslationLoaderRepositoryInterface|null $translationLoaderRepository = null,
        protected LoaderPluginManager|null $loaderPluginManager = null
    ) {}

    /**
     * {@inheritdoc}
     */
    public function createTranslatorAdapter($locale)
    {
        $t = new Translator();
        if ($this->loaderPluginManager !== null) {
            $t->setPluginManager($this->loaderPluginManager);
        }
        $t->setCache(new LaminasCacheAdapter($this->cache));

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
