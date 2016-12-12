<?php

namespace Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Localization\Translator\Translation\Loader\AbstractTranslationLoader;
use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;
use Concrete\Core\Package\PackageList;

/**
 * Translation loader that loads the package translations for the Zend translation
 * adapter.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class PackagesTranslationLoader extends AbstractTranslationLoader
{

    /**
     * {@inheritDoc}
     */
    public function loadTranslations(TranslatorAdapterInterface $translatorAdapter)
    {
        $config = $this->app->make('config');
        if ($config->get('app.bootstrap.packages_loaded') === true) {
            $pkgList = PackageList::get();
            $translator = $translatorAdapter->getTranslator();
            $locale = $translatorAdapter->getLocale();
            foreach ($pkgList->getPackages() as $pkg) {
                $path = $pkg->getPackagePath() . '/' . DIRNAME_LANGUAGES;
                $languageFile = "$path/$locale/LC_MESSAGES/messages.mo";
                if (is_file($languageFile)) {
                    $translator->addTranslationFile('gettext', $languageFile);
                }
            }
        }
    }

}
