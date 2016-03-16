<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\CoreTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory;
use Concrete\Core\Support\Facade\Facade;
use Exception;
use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\CoreTranslationLoader
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class CoreTranslationLoaderTest extends PHPUnit_Framework_TestCase
{

    protected $adapter;
    protected $loader;

    public static function setUpBeforeClass()
    {
        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_APPLICATION)) {
            throw new Exception("Cannot write to the application directory for the testing purposes. Please check permissions!");
        } elseif ($filesystem->exists(DIR_APPLICATION . '/languages')) {
            throw new Exception("The languages directory already exists in the application folder. It should not exist for the testing purposes.");
        }

        $langDir = __DIR__ . '/fixtures/languages/fi_FI';
        $appLangDir = DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/fi_FI';
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
        $this->loader = new CoreTranslationLoader($app);
    }

    public function testLoadTranslations()
    {
        $this->loader->loadTranslations($this->adapter);

        $this->assertEquals("Tervehdys ytimestä!", $this->adapter->translate("Hello from core!"));
    }

}
