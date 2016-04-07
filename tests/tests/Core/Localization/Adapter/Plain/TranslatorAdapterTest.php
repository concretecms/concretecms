<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Plain;

use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapter;
use PHPUnit_Framework_TestCase;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapter
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterTest extends PHPUnit_Framework_TestCase
{

    protected $adapter;

    protected function setUp()
    {
        $this->adapter = new TranslatorAdapter();
    }

    public function testGetTranslator()
    {
        $this->assertNull($this->adapter->getTranslator());
    }

    public function testSetAndGetLocale()
    {
        $this->assertNull($this->adapter->getLocale());

        $this->adapter->setLocale('en_US');
        $this->assertEquals('en_US', $this->adapter->getLocale());

        $this->adapter->setLocale('tlh_US');
        $this->assertEquals('tlh_US', $this->adapter->getLocale());
    }

    public function testTranslate()
    {
        $this->assertEquals('Test String', $this->adapter->translate('Test String'));
    }

    public function testFormatTranslate()
    {
        $this->assertEquals('Hello John!', $this->adapter->translate('Hello %s!', 'John'));
    }

    public function testTranslatePlural()
    {
        $singular = "One Thing";
        $plural = "Many Things";

        $this->assertEquals($singular, $this->adapter->translatePlural($singular, $plural, 1));
        $this->assertEquals($plural, $this->adapter->translatePlural($singular, $plural, 2));
        $this->assertEquals($plural, $this->adapter->translatePlural($singular, $plural, 10));
        $this->assertEquals($plural, $this->adapter->translatePlural($singular, $plural, 0));
    }

    public function testFormatTranslatePlural()
    {
        $singular = "Hello Little Minion!";
        $plural = "Hello %d Little Minions!";

        $this->assertEquals($singular, $this->adapter->translatePlural($singular, $plural, 1));
        $this->assertEquals(sprintf($plural, 2), $this->adapter->translatePlural($singular, $plural, 2));
    }

    public function testTranslateContext()
    {
        $this->assertEquals('Test String', $this->adapter->translateContext('context', 'Test String'));
    }

    public function testFormatTranslateContext()
    {
        $this->assertEquals('Test String', $this->adapter->translateContext('context', 'Test %s', 'String'));
    }

}
