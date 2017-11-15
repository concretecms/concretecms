<?php

namespace Concrete\Tests\Localization\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\TestHelpers\Localization\Adapter\Zend\Translation\Loader\Gettext\Fixtures\MultilingualDetector;
use Concrete\TestHelpers\Localization\LocalizationTestsBase;
use Illuminate\Filesystem\Filesystem;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class SiteTranslationLoaderTest extends LocalizationTestsBase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $filesystem = new Filesystem();

        $langDir = DIR_TESTS . '/assets/Localization/Adapter/Zend/Translation/Loader/Gettext/languages/site';
        $appLangDir = static::getTranslationsFolder() . '/site';
        $filesystem->copyDirectory($langDir, $appLangDir);
    }

    protected function setUp()
    {
        $factory = new TranslatorAdapterFactory();
        $this->adapter = $factory->createTranslatorAdapter('fi_FI');

        $app = Facade::getFacadeApplication();
        $this->loader = new SiteTranslationLoader($app);

        // Override the multilingual detector so that we can run these tests
        // without the need to create any database tables.
        $app->bind('multilingual/detector', function () {
            return new MultilingualDetector();
        });
    }

    protected function tearDown()
    {
        $app = Facade::getFacadeApplication();
        $msp = $app->make('Concrete\Core\Multilingual\MultilingualServiceProvider');
        $msp->register();
    }

    public function testLoadTranslations()
    {
        $this->loader->loadTranslations($this->adapter);

        $this->assertEquals('Tervehdys sivustolta!', $this->adapter->translate('Hello from site!'));
    }
}
