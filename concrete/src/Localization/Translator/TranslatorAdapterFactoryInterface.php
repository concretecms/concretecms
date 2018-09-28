<?php
namespace Concrete\Core\Localization\Translator;

/**
 * Translator adapter factories provide factory method for creating a new
 * translator adapter.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
interface TranslatorAdapterFactoryInterface
{
    /**
     * Creates a translator adapter for the given locale.
     *
     * @param string $locale
     *
     * @return TranslatorAdapterInterface
     */
    public function createTranslatorAdapter($locale);
}
