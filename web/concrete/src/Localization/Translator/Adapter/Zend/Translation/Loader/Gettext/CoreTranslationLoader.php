<?php

namespace Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Localization\Translator\Translation\Loader\AbstractTranslationLoader;
use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

/**
 * Translation loader that loads the concrete5 core translations for the Zend
 * translation adapter.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class CoreTranslationLoader extends AbstractTranslationLoader
{

    /**
     * {@inheritDoc}
     */
    public function loadTranslations(TranslatorAdapterInterface $translatorAdapter)
    {
        $locale = $translatorAdapter->getLocale();
        $languageFile = DIR_LANGUAGES . "/$locale/LC_MESSAGES/messages.mo";
        if (!is_file($languageFile)) {
            $languageFile = DIR_LANGUAGES_CORE . "/$locale/LC_MESSAGES/messages.mo";
            if (!is_file($languageFile)) {
                $languageFile = '';
            }
        }
        if ($languageFile !== '') {
            $translator = $translatorAdapter->getTranslator();
            $translator->addTranslationFile('gettext', $languageFile);
            if (is_object($cache = $translator->getCache())) {
                $cache->flush();
            }
        }
    }

}