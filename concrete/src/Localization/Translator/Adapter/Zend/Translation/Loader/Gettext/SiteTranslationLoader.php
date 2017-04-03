<?php
namespace Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Localization\Translator\Translation\Loader\AbstractTranslationLoader;
use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

/**
 * Translation loader that loads the site interface translations for the Zend
 * translation adapter.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class SiteTranslationLoader extends AbstractTranslationLoader
{
    /**
     * {@inheritdoc}
     */
    public function loadTranslations(TranslatorAdapterInterface $translatorAdapter)
    {
        $languageFile = DIR_LANGUAGES_SITE_INTERFACE . '/' . $translatorAdapter->getLocale() . '.mo';
        if (is_file($languageFile)) {
            $translator = $translatorAdapter->getTranslator();
            $translator->addTranslationFile('gettext', $languageFile);
        }
    }
}
