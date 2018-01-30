<?php

namespace Concrete\TestHelpers\Localization\Fixtures;

use Concrete\Core\Localization\Translator\Translation\Loader\AbstractTranslationLoader;
use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

/**
 * Simple translation loader that can be used for the tests.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TestUpdatedTranslationLoader extends AbstractTranslationLoader
{
    /**
     * {@inheritdoc}
     */
    public function loadTranslations(TranslatorAdapterInterface $translatorAdapter)
    {
        $translator = $translatorAdapter->getTranslator();
        $translator->addTranslationFile('phparray', DIR_TESTS . '/assets/Localization/translations_updated.php');
    }
}
