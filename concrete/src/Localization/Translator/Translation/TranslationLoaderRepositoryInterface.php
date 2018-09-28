<?php
namespace Concrete\Core\Localization\Translator\Translation;



/**
 * Translation loader repositories provide a way to store multiple translation
 * loaders.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
interface TranslationLoaderRepositoryInterface
{
    /**
     * Registers the translation loader for the specified handle.
     *
     * @param string $handle
     * @param TranslationLoaderInterface $loader
     *
     * @return TranslationLoaderInterface
     */
    public function registerTranslationLoader($handle, TranslationLoaderInterface $loader);

    /**
     * Gets the translation loader for the specified handle.
     *
     * @param string $handle
     *
     * @return TranslationLoaderInterface
     */
    public function getTranslationLoader($handle);

    /**
     * Determines whether a translation loader with the specified handle has
     * been registered.
     *
     * @param string $handle
     *
     * @return bool
     */
    public function hasTranslationLoader($handle);

    /**
     * Removes the translation loader with the specified handle.
     *
     * @param string $handle
     */
    public function removeTranslationLoader($handle);

    /**
     * Gets all registered translation providers.
     *
     * @return array
     */
    public function getTranslationLoaders();
}
