<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Core;

use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Localization\Translator\Adapter\Core\TranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapterFactory as PlainTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory as ZendTranslatorAdapterFactory;
use PHPUnit_Framework_TestCase;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Core\TranslatorAdapterFactory
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterFactoryTest extends PHPUnit_Framework_TestCase
{

    protected $factory;

    protected function setUp()
    {
        $app = Facade::getFacadeApplication();

        $config = $app->make('config');
        $plainFactory = new PlainTranslatorAdapterFactory();
        $zendFactory = new ZendTranslatorAdapterFactory();

        $this->factory = new TranslatorAdapterFactory($config, $plainFactory, $zendFactory);
    }

    public function testCreateTranslatorAdapterWithBaseLocale()
    {
        $adapter = $this->factory->createTranslatorAdapter('en_US');

        $this->assertInstanceOf('Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapter', $adapter);
    }

    public function testCreateTranslatorAdapterWithCustomLocale()
    {
        $adapter = $this->factory->createTranslatorAdapter('tlh_US');

        $this->assertInstanceOf('Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapter', $adapter);

    }

}