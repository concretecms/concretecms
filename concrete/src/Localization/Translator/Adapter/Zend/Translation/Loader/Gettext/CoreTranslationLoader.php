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
            $languageFile = $this->getAppLanguageFilePath($localeIDAlternative);
            if (!is_file($languageFile)) {
                $languageFile = $this->getCoreLanguageFilePath($localeIDAlternative);
                if (!is_file($languageFile)) {
                    $languageFile = null;
                }
            }
            if ($languageFile !== null) {
                $result = $languageFile;
                break;
            }
        }

        return $result;
    }

    /**
     * Get the full path to the file containing the localized strings for a specific locale (in the application directory).
     *
     * @param string $localeID The ID of the locale
     *
     * @return string
     */
    private function getAppLanguageFilePath($localeID)
    {
        return DIR_LANGUAGES . '/' . $localeID . '/LC_MESSAGES/messages.mo';
    }

    /**
     * Get the full path to the file containing the localized strings for a specific locale (in the concrete directory).
     *
     * @param string $localeID The ID of the locale
     *
     * @return string
     */
    private function getCoreLanguageFilePath($localeID)
    {
        return DIR_LANGUAGES_CORE . '/' . $localeID . '/LC_MESSAGES/messages.mo';
    }
}
