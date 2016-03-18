<?php

namespace Concrete\Tests\Core\Localization\Fixtures;

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
     * {@inheritDoc}
     */
    public function loadTranslations(TranslatorAdapterInterface $translatorAdapter)
    {
        $translator = $translatorAdapter->getTranslator();
        $translator->addTranslationFile('phparray', __DIR__ . '/translations_updated.php');
    }

}