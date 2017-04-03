<?php
namespace Concrete\Core\Localization\Translator\Adapter\Zend;

use Concrete\Core\Cache\Adapter\ZendCacheDriver;
use Concrete\Core\Localization\Translator\Translation\TranslationLoaderRepositoryInterface;
use Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface;
use Zend\I18n\Translator\Translator;

/**
 * Provides a factory method to create translator objects for the Zend
 * translator.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterFactory implements TranslatorAdapterFactoryInterface
{
    /** @var TranslationLoaderRepositoryInterface */
    protected $translationLoaderRepository;

    /**
     * {@inheritdoc}
     */
    public function __construct(TranslationLoaderRepositoryInterface $translationLoaderRepository = null)
    {
        $this->translationLoaderRepository = $translationLoaderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslatorAdapter($locale)
    {
        $cache = new ZendCacheDriver('cache/expensive');

        $t = new Translator();
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
