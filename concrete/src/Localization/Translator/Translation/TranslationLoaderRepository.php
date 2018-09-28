<?php
namespace Concrete\Core\Localization\Translator\Translation;



/**
 * Basic implementation of the {@link TranslationLoaderRepositoryInterface}.
 * Stores the translation loaders in a local array.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslationLoaderRepository implements TranslationLoaderRepositoryInterface
{
    protected $loaders = [];

    /**
     * {@inheritdoc}
     */
    public function registerTranslationLoader($handle, TranslationLoaderInterface $loader)
    {
        $this->loaders[$handle] = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationLoader($handle)
    {
        if ($this->hasTranslationLoader($handle)) {
            return $this->loaders[$handle];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTranslationLoader($handle)
    {
        return array_key_exists($handle, $this->loaders);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTranslationLoader($handle)
    {
        if ($this->hasTranslationLoader($handle)) {
            unset($this->loaders[$handle]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationLoaders()
    {
        return $this->loaders;
    }
}
