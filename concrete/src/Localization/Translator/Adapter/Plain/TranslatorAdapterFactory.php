<?php
namespace Concrete\Core\Localization\Translator\Adapter\Plain;

use Concrete\Core\Localization\Translator\TranslatorAdapterFactoryInterface;

/**
 * Provides a factory method to create translator objects for the plain
 * translator.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterFactory implements TranslatorAdapterFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createTranslatorAdapter($locale)
    {
        $adapter = new TranslatorAdapter();
        $adapter->setLocale($locale);

        return $adapter;
    }
}
