<?php

namespace Concrete\Tests\Core\Localization\Translator\Fixtures;

use Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface;

/**
 * Provides a factory method to create translator objects for the dummy
 * translator used in tests.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class DummyTranslatorAdapterFactory implements TranslatorAdapterFactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function createTranslatorAdapter($locale)
    {
        $adapter = new DummyTranslatorAdapter();
        $adapter->setLocale($locale);

        return $adapter;
    }

}
