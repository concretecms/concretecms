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
        $locale = $translatorAdapter->getLocale();
        $languageFile = $this->locateLanguageFile($locale);
        if ($languageFile !== null) {
            $translator = $translatorAdapter->getTranslator();
            $translator->addTranslationFile('gettext', $languageFile);
        }
    }

    /**
     * Get the full path to the file containing the localized strings for a specific locale.
     *
     * @param string $localeID The ID of the locale
     *
     * @return string|null Returns the full path of the file if it exists, null otherwise
     */
    private function locateLanguageFile($localeID)
    {
        $localeIDAlternatives = $this->getLocaleIDAlternatives($localeID);
        $result = null;
        foreach ($localeIDAlternatives as $localeIDAlternative) {
            $languageFile = $this->getLanguageFilePath($localeIDAlternative);
            if (is_file($languageFile)) {
                $result = $languageFile;
                break;
            }
        }

        return $result;
    }

    /**
     * Get the full path to the file containing the localized strings for a specific locale.
     *
     * @param string $localeID The ID of the locale
     *
     * @return string
     */
    private function getLanguageFilePath($localeID)
    {
        return DIR_LANGUAGES_SITE_INTERFACE . '/' . $localeID . '.mo';
    }
}
