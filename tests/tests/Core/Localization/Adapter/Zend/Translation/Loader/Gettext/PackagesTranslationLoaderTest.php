<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext;

use Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\PackagesTranslationLoader;
use Concrete\Core\Localization\Translator\Adapter\Zend\TranslatorAdapterFactory;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Facade;
use ConcreteDatabaseTestCase;
use Exception;
use Illuminate\Filesystem\Filesystem;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\PackagesTranslationLoader
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class PackagesTranslationLoaderTest extends ConcreteDatabaseTestCase
{

    private static $packagesInstalled = false;

    protected $tables = array('Packages');

    /**
     * Move a couple of test packages to the packages folder to be used by
     * these tests.
     */
    public static function setUpBeforeClass()
    {
        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_PACKAGES)) {
            throw new Exception("Cannot write to the packages directory for the testing purposes. Please check permissions!");
        }

        $packages = self::getTestPackages();

        // First make sure that none of the packages already exist
        foreach ($packages as $pkg => $dir) {
            if ($filesystem->exists(DIR_PACKAGES . '/' . $pkg)) {
                throw new Exception("A package directory for a package named ${pkg} already exists. It cannot exist prior to running these tests.");
            }
        }
        // Then, move the package folders to the package folder
        foreach ($packages as $pkg => $dir) {
            $target = DIR_PACKAGES . '/' . $pkg;
            $filesystem->copyDirectory($dir, $target);
        }
    }

    /**
     * Delete all the temporary package folders from the packages directory
     * after all tests have run.
     */
    public static function tearDownAfterClass()
    {
        $installPackages = self::getTestPackages();

        $filesystem = new Filesystem();
        foreach ($installPackages as $pkg => $dir) {
            $target = DIR_PACKAGES . '/' . $pkg;
            $filesystem->deleteDirectory($target);
        }

        parent::tearDownAfterClass();
    }

    private static function getTestPackages()
    {
        $pkgSource = __DIR__ . '/fixtures/packages';
        $packages = array();

        $filesystem = new Filesystem();
        foreach ($filesystem->directories($pkgSource) as $dir) {
            $packages[basename($dir)] = $dir;
        }

        return $packages;
    }

    protected function setUp()
    {
        parent::setUp();

        // The setUp() procedures install the database table required for
        // installing the packages. This is why we need to install these
        // AFTER that has run. There is no need to uninstall the packages
        // during tearDown() because the Packages table is already being
        // dropped by the parent class.
        $installPackages = self::getTestPackages();
        foreach ($installPackages as $pkg => $dir) {
            $package = Package::getClass($pkg);
            $p = $package->install();
        }

        $factory = new TranslatorAdapterFactory();
        $this->adapter = $factory->createTranslatorAdapter('fi_FI');

        $app = Facade::getFacadeApplication();
        $this->loader = new PackagesTranslationLoader($app);

        // Mark the packages as loaded for the translations lodader to be
        // able to load any translations.
        $app->make('config')->set('app.bootstrap.packages_loaded', true);
    }

    public function testLoadTranslations()
    {
        $this->loader->loadTranslations($this->adapter);

        $this->assertEquals("Tyhjä", $this->adapter->translate("Dummy"));
        $this->assertEquals("Toinen tyhjä", $this->adapter->translate("Dummy Other"));
    }

}
