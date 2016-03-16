<?php

namespace Concrete\Tests\Core\Localization\Translator\Translation;

use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Translator\TranslatorAdapterRepository;
use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapterFactory as PlainTranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory as ZendTranslatorAdapterFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Events;
use Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\Fixtures\MultilingualDetector;
use Exception;
use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;
use Punic\Language as PunicLanguage;
use ReflectionClass;
use Symfony\Component\ClassLoader\MapClassLoader;
use Zend\I18n\Translator\Translator as ZendTranslator;

/**
 * Tests for:
 * Concrete\Core\Localization\Localization
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class LocalizationTest extends PHPUnit_Framework_TestCase
{

    protected $loc;

    public static function setUpBeforeClass()
    {
        // Move language directories to the application
        $source = __DIR__ . '/fixtures/languages';
        $target = DIR_APPLICATION . '/' . DIRNAME_LANGUAGES;

        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_APPLICATION)) {
            throw new Exception("Cannot write to the application directory for the testing purposes. Please check permissions!");
        } elseif ($filesystem->exists($target)) {
            throw new Exception("The languages directory already exists in the application folder. It should not exist for the testing purposes.");
        }

        $filesystem->copyDirectory($source, $target);
    }

    public static function tearDownAfterClass()
    {
        // Delete language directories from the application
        $filesystem = new Filesystem();
        $filesystem->deleteDirectory(DIR_APPLICATION . '/' . DIRNAME_LANGUAGES);
    }

    protected function setUp()
    {
        $this->loc = new Localization();

        $translatorAdapterFactory = new PlainTranslatorAdapterFactory();
        $repository = new TranslatorAdapterRepository($translatorAdapterFactory);
        $this->loc->setTranslatorAdapterRepository($repository);
    }

    protected function tearDown()
    {
        // Some of the tests might be rewriting some core components through
        // the IoC container which we revert back here. Also, the localization
        // instance gets reset in some of the static tests because of which we
        // revert it back here to its original state.
        $app = Facade::getFacadeApplication();

        $lsp = $app->make('Concrete\Core\Localization\LocalizationEssentialServiceProvider');
        $lsp->register();

        $msp = $app->make('Concrete\Core\Multilingual\MultilingualServiceProvider');
        $msp->register();

        $loc = Localization::getInstance();
        $loc->setActiveContext('system');
    }

    /**
     * @expectedException Exception
     */
    public function testTranslatorAdapterRepositoryRequired()
    {
        $loc = new Localization();
        $loc->getTranslatorAdapterRepository();
    }

    public function testGetTranslatorAdapterRepository()
    {
        $this->assertInstanceOf('Concrete\Core\Localization\Translator\TranslatorAdapterRepositoryInterface', $this->loc->getTranslatorAdapterRepository());
    }

    public function testSetTranslatorAdapterRepository()
    {
        // We are testing the localization library here, not the translator
        // adapters, so using the plain translator adapter should be perfectly
        // OK for testing purposes. The other adapters (and their factories)
        // are tested with separate tests.
        $translatorAdapterFactory = new PlainTranslatorAdapterFactory();
        $repository = new TranslatorAdapterRepository($translatorAdapterFactory);

        $loc = new Localization();
        $loc->setTranslatorAdapterRepository($repository);

        $this->assertEquals($repository, $loc->getTranslatorAdapterRepository());
    }

    public function testSetGetActiveContext()
    {
        $this->assertNull($this->loc->getActiveContext());

        $this->loc->setActiveContext('test');
        $this->assertEquals('test', $this->loc->getActiveContext());
    }

    public function testDefaultContextLocale()
    {
        $this->loc->setActiveContext('test');
        $this->assertEquals(Localization::BASE_LOCALE, $this->loc->getContextLocale('test'));
    }

    public function testRevertActiveContext()
    {
        $this->loc->setActiveContext('test1');
        $this->loc->setActiveContext('test2');
        $this->loc->setActiveContext('test3');

        $this->loc->revertActiveContext();
        $this->assertEquals('test2', $this->loc->getActiveContext());

        $this->loc->revertActiveContext();
        $this->assertEquals('test1', $this->loc->getActiveContext());
    }

    public function testGetTranslatorAdapter()
    {
        $this->loc->setActiveContext('test');

        $this->assertInstanceOf('Concrete\Core\Localization\Translator\TranslatorAdapterInterface', $this->loc->getTranslatorAdapter('test'));
    }

    /**
     * @expectedException Exception
     */
    public function testGetUnexistingContextTranslatorAdapter()
    {
        $this->loc->getTranslatorAdapter('test');
    }

    public function testGetActiveTranslatorAdapter()
    {
        $this->loc->setActiveContext('test');
        $this->assertInstanceOf('Concrete\Core\Localization\Translator\TranslatorAdapterInterface', $this->loc->getActiveTranslatorAdapter());
    }

    /**
     * @expectedException Exception
     */
    public function testGetUnexistingActiveTranslatorAdapter()
    {
        $this->loc->getActiveTranslatorAdapter();
    }

    public function testSetGetContextLocale()
    {
        $this->loc->setContextLocale('test1', 'en_US');
        $this->loc->setContextLocale('test2', 'tlh_US');
        $this->loc->setContextLocale('test3', 'xx_XX');

        $this->assertEquals('en_US', $this->loc->getContextLocale('test1'));
        $this->assertEquals('tlh_US', $this->loc->getContextLocale('test2'));
        $this->assertEquals('xx_XX', $this->loc->getContextLocale('test3'));

        $this->loc->setContextLocale('test2', 'fi_FI');

        $this->assertEquals('en_US', $this->loc->getContextLocale('test1'));
        $this->assertEquals('fi_FI', $this->loc->getContextLocale('test2'));
        $this->assertEquals('xx_XX', $this->loc->getContextLocale('test3'));
    }

    public function testSetGetLocale()
    {
        $this->loc->setActiveContext('test');

        $this->loc->setLocale('tlh_US');
        $this->assertEquals('tlh_US', $this->loc->getContextLocale('test'));
        $this->assertEquals('tlh_US', $this->loc->getLocale());

        $this->loc->setLocale('fi_FI');
        $this->assertEquals('fi_FI', $this->loc->getContextLocale('test'));
        $this->assertEquals('fi_FI', $this->loc->getLocale());
    }

    public function testSetLocaleCallbackEvent()
    {
        $app = Facade::getFacadeApplication();
        $origDirector = $app->make('director');

        $director = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->setMethods(array('dispatch'))
            ->getMock();

        // Force the events to use the new director to be bound.
        Events::clearResolvedInstance("director");

        // Make the mock object to be used as the events backend through IoC.
        $app->bindShared('director', function() use ($director) {
            return $director;
        });

        $director->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo('on_locale_load'),
                $this->isInstanceOf('Symfony\Component\EventDispatcher\GenericEvent')
            );

        $director = $app->make('director');
        $director->addListener(
            'on_locale_load',
            function($event) {
                $locale = $event->getArgument('locale');
                $this->assertEquals('tlh_US', $locale);
            }
        );
        $this->loc->setLocale('tlh_US');

        // After the test has run, make sure the original director is being used.
        Events::clearResolvedInstance("director");
        $app->bindShared('director', function() use ($origDirector) {
            return $origDirector;
        });
    }

    public function testRemoveLoadedTranslationAdapters()
    {
        $this->loc->setActiveContext('test');

        $this->loc->setContextLocale('test', 'en_US');
        $this->loc->getActiveTranslatorAdapter();

        $this->loc->setContextLocale('test', 'tlh_US');
        $this->loc->getActiveTranslatorAdapter();

        $this->loc->setContextLocale('test', 'xx_XX');
        $this->loc->getActiveTranslatorAdapter();

        $this->loc->removeLoadedTranslationAdapters();

        $rep = $this->loc->getTranslatorAdapterRepository();

        $reflection = new ReflectionClass($rep);
        $property = $reflection->getProperty('adapters');
        $property->setAccessible(true);
        $adapters = $property->getValue($rep);

        $this->assertEquals(0, count($adapters));
    }

    public function testStaticGetInstance()
    {
        $this->setupStaticTest();

        $this->assertInstanceOf('Concrete\Core\Localization\Localization', Localization::getInstance());
        $this->assertEquals($this->loc, Localization::getInstance());
    }

    public function testStaticChangeLocale()
    {
        $this->setupStaticTest();

        Localization::changeLocale('en_US');
        $this->assertEquals('en_US', $this->loc->getLocale());

        Localization::changeLocale('tlh_US');
        $this->assertEquals('tlh_US', $this->loc->getLocale());

        Localization::changeLocale('xx_XX');
        $this->assertEquals('xx_XX', $this->loc->getLocale());
    }

    public function testStaticActiveLocale()
    {
        $this->loc->setContextLocale('test', 'tlh_US');
        $this->loc->setActiveContext('test');
        $this->setupStaticTest();

        $this->assertEquals('tlh_US', Localization::activeLocale());
    }

    public function testStaticActiveLanguage()
    {
        $this->loc->setContextLocale('test', 'tlh_US');
        $this->loc->setActiveContext('test');
        $this->setupStaticTest();

        $this->assertEquals('tlh', Localization::activeLanguage());
    }

    public function testStaticGetAvailableInterfaceLanguages()
    {
        $langs = Localization::getAvailableInterfaceLanguages();
        $this->assertEquals(array('en_GB', 'fi_FI', 'fr_FR'), $langs);
    }

    public function testStaticGetAvailableInterfaceLanguageDescriptions()
    {
        $displayLocale = Localization::BASE_LOCALE;
        $expected = array(
            'en_US' => PunicLanguage::getName('en_US', $displayLocale),
            'en_GB' => PunicLanguage::getName('en_GB', $displayLocale),
            'fi_FI' => PunicLanguage::getName('fi_FI', $displayLocale),
            'fr_FR' => PunicLanguage::getName('fr_FR', $displayLocale),
        );

        $langs = Localization::getAvailableInterfaceLanguageDescriptions();
        $this->assertEquals($expected, $langs);
    }

    public function testStaticGetLanguageDescription()
    {
        $displayLocale = Localization::BASE_LOCALE;
        foreach (array('en_GB', 'fi_FI', 'fr_FR') as $locale) {
            $expected = PunicLanguage::getName($locale, $displayLocale);
            $this->assertEquals($expected, Localization::getLanguageDescription($locale, $displayLocale));
        }
    }

    public function testStaticClearCache()
    {
        $app = Facade::getFacadeApplication();

        $loc = $this->getMockBuilder('Concrete\Core\Localization\Localization')
            ->setMethods(array('removeLoadedTranslationAdapters'))
            ->getMock();

        // Make the mock object to be used as the events backend through IoC.
        $app->bindShared('Concrete\Core\Localization\Localization', function() use ($loc) {
            return $loc;
        });

        $loc->expects($this->once())
            ->method('removeLoadedTranslationAdapters');

        Localization::clearCache();
    }

    /**
     * Note: The tested method is deprecated so this test can be removed if
     *       the method is removed in the future.
     *
     * @deprecated
     */
    public function testGetActiveTranslateObject()
    {
        $this->loc->setActiveContext('test');

        // The plain translator adapter does not have a translator object
        // associated with it, so it should be returning null.
        $this->assertNull($this->loc->getActiveTranslateObject());
    }

    /**
     * Note: The tested method is deprecated so this test can be removed if
     *       the method is removed in the future.
     *
     * @deprecated
     */
    public function testStaticGetTranslate()
    {
        $this->loc->setActiveContext('test');
        $this->setupStaticTest();

        // The plain translator adapter does not have a translator object
        // associated with it, so it should be returning null.
        $this->assertNull(Localization::getTranslate());
    }

    /**
     * These tests should already be tested when testing the site translation
     * loader but this assures that the deprecated wrapper method within the
     * Localization class also works properly.
     *
     * Note: The tested method is deprecated so this test can be removed if
     *       the method is removed in the future.
     *
     * @deprecated
     */
    public function testStaticSetupSiteLocalization()
    {
        return;

        $siteTranslatorLoaderTestPath = __DIR__ . '/Adapter/Zend/Translation/Loader/Gettext';

        // Move translation files
        $langDir = $siteTranslatorLoaderTestPath . '/fixtures/' . DIRNAME_LANGUAGES . '/site';
        $appLangDir = DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/site';

        $filesystem = new Filesystem();
        if ($filesystem->exists($appLangDir)) {
            throw new Exception("The site languages directory already exists in the application folder. It should not exist for the testing purposes.");
        }
        $filesystem->copyDirectory($langDir, $appLangDir);

        // Make the MultilingualDetector override available
        $loader = new MapClassLoader([
            'Concrete\\Tests\\Core\\Localization\\Translator\\Adapter\\Zend\\Translation\\Loader\\Gettext\\Fixtures\\MultilingualDetector'
                => $siteTranslatorLoaderTestPath . '/fixtures/MultilingualDetector.php'
        ]);
        $loader->register();

        // Custom Localization instance for these tests
        $loc = new Localization();

        $translatorAdapterFactory = new ZendTranslatorAdapterFactory();
        $repository = new TranslatorAdapterRepository($translatorAdapterFactory);
        $loc->setTranslatorAdapterRepository($repository);

        $app = Facade::getFacadeApplication();
        $app->bind('Concrete\Core\Localization\Localization', function() use ($loc) {
            return $loc;
        });
        $app->bind('multilingual/detector', function() {
            return new MultilingualDetector();
        });

        $loc->setContextLocale('site', 'fi_FI');
        $loc->setActiveContext('site');

        // Test setting setup site localization for the active translator
        Localization::setupSiteLocalization();
        $translator = $loc->getActiveTranslatorAdapter();
        $this->assertEquals("Tervehdys sivustolta!", $translator->translate('Hello from site!'));

        // Test setting setup site localization for custom translator
        $translator = new ZendTranslator();
        $translator->setLocale("fi_FI");
        Localization::setupSiteLocalization($translator);
        $this->assertEquals("Tervehdys sivustolta!", $translator->translate('Hello from site!'));

        // Remove the added languages directory
        $filesystem->deleteDirectory($appLangDir);
    }

    private function setupStaticTest()
    {
        $loc = $this->loc;
        $app = Facade::getFacadeApplication();
        $app->bind('Concrete\Core\Localization\Localization', function() use ($loc) {
            return $loc;
        });
    }

}
