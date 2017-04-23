<?php
namespace Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Entity\Package;
use Concrete\Core\Localization\Translator\Translation\Loader\AbstractTranslationLoader;
use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;
use Concrete\Core\Package\PackageService;

/**
 * Translation loader that loads the package translations for the Zend translation
 * adapter.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class PackagesTranslationLoader extends AbstractTranslationLoader
{
    /**
     * {@inheritdoc}
     */
    public function loadTranslations(TranslatorAdapterInterface $translatorAdapter)
    {
        if ($this->app->isInstalled()) {
            $packageService = $this->app->make(PackageService::class);
            $pkgList = $packageService->getInstalledList();
            $translator = $translatorAdapter->getTranslator();
            $locale = $translatorAdapter->getLocale();
            foreach ($pkgList as $pkg) {
                $languageFile = $this->locateLanguageFile($pkg, $locale);
                if ($languageFile !== null) {
                    $translator->addTranslationFile('gettext', $languageFile);
                }
            }
        }
    }

    /**
     * Get the full path to the file containing the localized strings for a specific locale.
     *
     * @param Package $package The package for which you want the file path
     * @param string $localeID The ID of the locale
     *
     * @return string|null Returns the full path of the file if it exists, null otherwise
     */
    private function locateLanguageFile(Package $package, $localeID)
    {
        $localeIDAlternatives = $this->getLocaleIDAlternatives($localeID);
        $result = null;
        foreach ($localeIDAlternatives as $localeIDAlternative) {
            $languageFile = $package->getTranslationFile($localeIDAlternative);
            if (is_file($languageFile)) {
                $result = $languageFile;
                break;
            }
        }

        return $result;
    }
}
