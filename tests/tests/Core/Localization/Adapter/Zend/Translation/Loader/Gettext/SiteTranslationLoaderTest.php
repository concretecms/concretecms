<?php
namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\Fixtures\MultilingualDetector;
use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory;
use Concrete\Core\Support\Facade\Facade;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\ClassLoader\MapClassLoader;
use Concrete\Tests\Localization\LocalizationTestsBase;

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
        $loader = new MapClassLoader(array(
            'Concrete\\Tests\\Core\\Localization\\Translator\\Adapter\\Zend\\Translation\\Loader\\Gettext\\Fixtures\\MultilingualDetector' => __DIR__ . '/fixtures/MultilingualDetector.php',
        ));
        $loader->register();

        $filesystem = new Filesystem();

        $langDir = __DIR__ . '/fixtures/languages/site';
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

        $this->assertEquals("Tervehdys sivustolta!", $this->adapter->translate("Hello from site!"));
    }
}
