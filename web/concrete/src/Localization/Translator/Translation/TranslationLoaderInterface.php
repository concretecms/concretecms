<?php

namespace Concrete\Core\Localization\Translator\Translation;

use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

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