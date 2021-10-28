<?php

namespace Concrete\Tests\Localization\Adapter\Laminas;

use Concrete\Core\Localization\Translator\Adapter\Laminas\TranslatorAdapterFactory;
use Concrete\Tests\TestCase;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Laminas\TranslatorAdapterFactory.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterFactoryTest extends TestCase
{
    protected $factory;

    public function setUp():void
    {
        $this->factory = new TranslatorAdapterFactory();
    }

    public function testCreateTranslatorAdapter()
    {
        $adapter = $this->factory->createTranslatorAdapter('en_US');

        $this->assertInstanceOf('Concrete\Core\Localization\Translator\Adapter\Laminas\TranslatorAdapter', $adapter);
    }
}
