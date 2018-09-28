<?php
namespace Concrete\Core\Localization\Translator\Translation;

use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

/**
 * Translation loaders provide a standardized way to load new translations into
 * the translator adapters.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
interface TranslationLoaderInterface
{
    /**
     * Loads the translations from this loader type into the given translator
     * instance.
     *
     * The given translator instance should have the correct locale already set
     * for it which defines the loader which translations should be loaded.
     *
     * @param TranslatorAdapterInterface $translatorAdapter The translator object
     */
    public function loadTranslations(TranslatorAdapterInterface $translatorAdapter);
}
