<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\Fixtures\MultilingualDetector;
use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory;
use Concrete\Core\Support\Facade\Facade;
use Exception;
use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;
use Symfony\Component\ClassLoader\MapClassLoader;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class SiteTranslationLoaderTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        $loader = new MapClassLoader(array(
            'Concrete\\Tests\\Core\\Localization\\Translator\\Adapter\\Zend\\Translation\\Loader\\Gettext\\Fixtures\\MultilingualDetector'
                => __DIR__ . '/fixtures/MultilingualDetector.php'
        ));
        $loader->register();

        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_APPLICATION)) {
            return self::markTestSkipped(
                "Cannot write to the application directory for the testing purposes. Please check permissions!");
        } elseif ($filesystem->exists(DIR_APPLICATION . '/languages')) {
            return self::markTestSkipped(
                "The languages directory already exists in the application folder. It should not exist for the testing purposes.");
        }

        $langDir = __DIR__ . '/fixtures/' . DIRNAME_LANGUAGES . '/site';
        $appLangDir = DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/site';
        $filesystem->copyDirectory($langDir, $appLangDir);
    }

    public static function tearDownAfterClass()
    {
        $filesystem = new Filesystem();
        $filesystem->deleteDirectory(DIR_APPLICATION . '/' . DIRNAME_LANGUAGES);
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

        $this->assertEquals("Tervehdys sivustolta!", $this->adapter->translate("Hello from site!"));
    }

}
