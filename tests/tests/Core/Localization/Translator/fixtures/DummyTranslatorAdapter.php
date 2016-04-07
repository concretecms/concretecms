<?php

namespace Concrete\Tests\Core\Localization\Translator\Fixtures;

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
     *
     * @return null
     */
    public function getTranslator()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritDoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }


    /**
     * {@inheritDoc}
     */    public function translate($text)
    {
        return $text;
    }


    /**
     * {@inheritDoc}
     */
    public function translatePlural($singular, $plural, $number)
    {
        return $number == 1 ? $singular : $plural;
    }

    /**
     * {@inheritDoc}
     */
    public function translateContext($context, $text)
    {
        return $text;
    }

}
