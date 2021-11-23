<?php

namespace Concrete\Tests\Localization\Adapter\Core;

use Concrete\Core\Localization\Translator\Adapter\Core\TranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapterFactory as PlainTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Laminas\TranslatorAdapterFactory as LaminasTranslatorAdapterFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Tests\TestCase;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Core\TranslatorAdapterFactory.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslatorAdapterFactoryTest extends TestCase
{
    protected $factory;

    public function setUp():void
    {
        $app = Facade::getFacadeApplication();

        $config = $app->make('config');
        $plainFactory = new PlainTranslatorAdapterFactory();
        $laminasFactory = new LaminasTranslatorAdapterFactory();

        $this->factory = new TranslatorAdapterFactory($config, $plainFactory, $laminasFactory);
    }

    public function testCreateTranslatorAdapterWithBaseLocale()
    {
        $adapter = $this->factory->createTranslatorAdapter('en_US');

        $this->assertInstanceOf('Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapter', $adapter);
    }

    public function testCreateTranslatorAdapterWithCustomLocale()
    {
        $adapter = $this->factory->createTranslatorAdapter('tlh_US');

        $this->assertInstanceOf('Concrete\Core\Localization\Translator\Adapter\Laminas\TranslatorAdapter', $adapter);
    }
}
