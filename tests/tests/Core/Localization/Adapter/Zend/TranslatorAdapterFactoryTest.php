<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend;

use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory;
use PHPUnit_Framework_TestCase;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterFactoryTest extends PHPUnit_Framework_TestCase
{

    protected $factory;

    protected function setUp()
    {
        $this->factory = new TranslatorAdapterFactory();
    }

    public function testCreateTranslatorAdapter()
    {
        $adapter = $this->factory->createTranslatorAdapter('en_US');

        $this->assertInstanceOf('Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapter', $adapter);
    }

}
