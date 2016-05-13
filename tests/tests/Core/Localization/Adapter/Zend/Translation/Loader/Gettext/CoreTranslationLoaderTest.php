<?php
namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\CoreTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory;
use Concrete\Core\Support\Facade\Facade;
use Illuminate\Filesystem\Filesystem;
use Concrete\Tests\Localization\LocalizationTestsBase;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\CoreTranslationLoader.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class CoreTranslationLoaderTest extends LocalizationTestsBase
{
    private static $languagesDirectoryExisted = false;

    protected $adapter;
    protected $loader;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $filesystem = new Filesystem();
        $langDir = __DIR__ . '/fixtures/languages/fi_FI';
        $appLangDir = parent::getTranslationsFolder()  . '/fi_FI';
        $filesystem->copyDirectory($langDir, $appLangDir);
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
