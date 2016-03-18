<?php

namespace Concrete\Core\Localization\Translator\Translation;

use Concrete\Core\Application\Application;
use Concrete\Core\Localization\Translator\Translation\TranslationLoaderInterface;
use Zend\I18n\Translator\Translator;

/**
 * Basic implementation of the {@link TranslationLoaderRepositoryInterface}.
 * Stores the translation loaders in a local array.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslationLoaderRepository implements TranslationLoaderRepositoryInterface
{

    protected $loaders = array();

    /**
     * {@inheritDoc}
     */
    public function registerTranslationLoader($handle, TranslationLoaderInterface $loader)
    {
        $this->loaders[$handle] = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslationLoader($handle)
    {
        if ($this->hasTranslationLoader($handle)) {
            return $this->loaders[$handle];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasTranslationLoader($handle)
    {
        return array_key_exists($handle, $this->loaders);
    }

    /**
     * {@inheritDoc}
     */
    public function removeTranslationLoader($handle)
    {
        if ($this->hasTranslationLoader($handle)) {
            unset($this->loaders[$handle]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslationLoaders()
    {
        return $this->loaders;
    }

}