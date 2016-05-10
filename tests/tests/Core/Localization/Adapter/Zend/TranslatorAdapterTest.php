<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend;

use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapter;
use PHPUnit_Framework_TestCase;
use Zend\I18n\Translator\Translator;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapter
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterTest extends PHPUnit_Framework_TestCase
{

    protected $adapter;

    protected function setUp()
    {
        $translationsFile = __DIR__ . '/fixtures/translations.php';

        $translator = new Translator();
        $translator->addTranslationFile('phparray', $translationsFile);
        $translator->setLocale('xx_XX');

        $this->adapter = new TranslatorAdapter($translator);
    }

    public function testGetTranslator()
    {
        $translator = $this->adapter->getTranslator();
        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $translator);
    }

    public function testSetAndGetLocale()
    {
        $this->assertEquals('xx_XX', $this->adapter->getLocale());

        $this->adapter->setLocale('en_US');
        $this->assertEquals('en_US', $this->adapter->getLocale());

        $this->adapter->setLocale('tlh_US');
        $this->assertEquals('tlh_US', $this->adapter->getLocale());
    }

    public function testTranslate()
    {
        $this->assertEquals('A B!', $this->adapter->translate('Hello Translator!'));
    }

    public function testFormatTranslate()
    {
        $this->assertEquals('A John!', $this->adapter->translate('Hello %s!', 'John'));
    }

    public function testTranslatePlural()
    {
        $singular = "Yellow Cat";
        $plural = "Yellow Cats";

        $this->assertEquals("X Y", $this->adapter->translatePlural($singular, $plural, 1));
        $this->assertEquals("X Ys", $this->adapter->translatePlural($singular, $plural, 2));
        $this->assertEquals("X Ys", $this->adapter->translatePlural($singular, $plural, 10));
        $this->assertEquals("X Ys", $this->adapter->translatePlural($singular, $plural, 0));
    }

    public function testTranslatePluralWithNumber()
    {
        $singular = "One Yellow Cat";
        $plural = "%d Yellow Cats";

        $this->assertEquals("D X Y", $this->adapter->translatePlural($singular, $plural, 1));
        $this->assertEquals("2 X Ys", $this->adapter->translatePlural($singular, $plural, 2));
        $this->assertEquals("10 X Ys", $this->adapter->translatePlural($singular, $plural, 10));
        $this->assertEquals("0 X Ys", $this->adapter->translatePlural($singular, $plural, 0));
    }

    public function testFormatTranslatePlural()
    {
        $singular = "%d Yellow %s";
        $plural = "%d Yellow %s";
        $what = "Z";

        $this->assertEquals("1 X $what", $this->adapter->translatePlural($singular, $plural, 1, $what));
        $this->assertEquals("2 X $what", $this->adapter->translatePlural($singular, $plural, 2, $what));
        $this->assertEquals("10 X $what", $this->adapter->translatePlural($singular, $plural, 10, $what));
        $this->assertEquals("0 X $what", $this->adapter->translatePlural($singular, $plural, 0, $what));
    }

    public function testTranslateContext()
    {
        $this->assertEquals('E!', $this->adapter->translateContext('context', 'Welcome!'));
    }

    public function testFormatTranslateContext()
    {
        $who = "Z";

        $this->assertEquals("E $who!", $this->adapter->translateContext('context', 'Welcome %s!', $who));
    }

}
