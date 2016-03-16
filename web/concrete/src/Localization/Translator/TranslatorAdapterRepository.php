<?php

namespace Concrete\Core\Localization\Translator;

use Concrete\Core\Application\Application;
use Concrete\Core\Localization\Localization;
use Zend\I18n\Translator\Translator;

/**
 * Basic implementation of the {@link TranslatorAdapterRepositoryInterface}.
 * Stores the translator adapters in a local array.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterRepository implements TranslatorAdapterRepositoryInterface
{

    const KEY_SEPARATOR = '@';

    /** @var Application */
    protected $app;

    /** @var array */
    protected $adapters = array();

    /**
     * @param TranslatorFactoryInterface $translatorFactory
     */
    public function __construct(TranslatorAdapterFactoryInterface $translatorAdapterFactory)
    {
        $this->translatorAdapterFactory = $translatorAdapterFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function registerTranslatorAdapter($handle, $locale, TranslatorAdapterInterface $translatorAdater)
    {
        $key = $this->getKey($handle, $locale);
        $this->adapters[$key] = $translatorAdater;
    }

    /**
     * {@inheritDoc}
     */
    public function hasTranslatorAdapter($handle, $locale)
    {
        $key = $this->getKey($handle, $locale);
        return array_key_exists($key, $this->adapters);
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatorAdapter($handle, $locale)
    {
        if (!$this->hasTranslatorAdapter($handle, $locale)) {
            $adapter = $this->translatorAdapterFactory->createTranslatorAdapter($locale);
            $this->registerTranslatorAdapter($handle, $locale, $adapter);
        }
        $key = $this->getKey($handle, $locale);
        return $this->adapters[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function removeTranslatorAdapter($handle, $locale)
    {
        if ($this->hasTranslatorAdapter($handle, $locale)) {
            $key = $this->getKey($handle, $locale);
            unset($this->adapters[$key]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeTranslatorAdaptersWithHandle($handle)
    {
        foreach ($this->adapters as $key => $a) {
            if (preg_match('#^' . $handle . '\\' . static::KEY_SEPARATOR . '#', $key)) {
                unset($this->adapters[$key]);
            }
        }
    }

    /**
     * Generates a store key for the given handle and locale. The translator
     * for the combination of these two is stored in the local array with the
     * key returned by this method.
     *
     * @param $handle
     * @param $locale
     *
     * @return string
     */
    protected function getKey($handle, $locale)
    {
        return $handle . static::KEY_SEPARATOR . $locale;
    }

}
