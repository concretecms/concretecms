<?php

namespace Concrete\TestHelpers\Localization\Translator\Fixtures;

use Concrete\Core\Localization\Translator\TranslatorAdapterInterface;

/**
 * Dummy translator adapter for the tests.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class DummyTranslatorAdapter implements TranslatorAdapterInterface
{
    /** @var string */
    protected $locale;

    /**
     * The dummy translator does not have any translator object attached to it,
     * so null is returned instead.
     */
    public function getTranslator()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function translate($text)
    {
        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function translatePlural($singular, $plural, $number)
    {
        return $number == 1 ? $singular : $plural;
    }

    /**
     * {@inheritdoc}
     */
    public function translateContext($context, $text)
    {
        return $text;
    }
}
