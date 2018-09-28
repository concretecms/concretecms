<?php
namespace Concrete\Core\Localization\Translator;

use Concrete\Core\Application\Application;
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
    protected $adapters = [];

    /**
     * @param TranslatorFactoryInterface $translatorFactory
     */
    public function __construct(TranslatorAdapterFactoryInterface $translatorAdapterFactory)
    {
        $this->translatorAdapterFactory = $translatorAdapterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function registerTranslatorAdapter($handle, $locale, TranslatorAdapterInterface $translatorAdater)
    {
        $key = $this->getKey($handle, $locale);
        $this->adapters[$key] = $translatorAdater;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTranslatorAdapter($handle, $locale)
    {
        $key = $this->getKey($handle, $locale);

        return isset($this->adapters[$key]);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function removeTranslatorAdapter($handle, $locale)
    {
        if ($this->hasTranslatorAdapter($handle, $locale)) {
            $key = $this->getKey($handle, $locale);
            unset($this->adapters[$key]);
        }
    }

    /**
     * {@inheritdoc}
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
