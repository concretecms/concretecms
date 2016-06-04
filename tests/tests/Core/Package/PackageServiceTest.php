<?php

namespace Concrete\Tests\Core\Package;

use Illuminate\Filesystem\Filesystem;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Application;

/**
 * PackageServiceTest
 * 
 * @group ORM setup
 */
class PackageServiceTest extends \ConcreteDatabaseTestCase
{
    protected $metadatas = array(
        'Concrete\Core\Entity\Package',
    );
    
    protected $app;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    
    /**
     * Move a couple of test packages to the packages folder to be used by
     * these tests.
     * Borrowed from Antti Hukkanen <antti.hukkanen@mainiotech.fi>
     */
    public static function setUpBeforeClass()
    {
        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_PACKAGES)) {
            throw new Exception("Cannot write to the packages directory for the testing purposes. Please check permissions!");
        }

        $packages = self::getTestPackagesForCopy();

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
    
    protected function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
        $this->em = \ORM::entityManager();

        // The setUp() procedures install the database table required for
        // installing the packages. This is why we need to install these
        // AFTER that has run. There is no need to uninstall the packages
        // during tearDown() because the Packages table is already being
        // dropped by the parent class.
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $installPackages = self::getTestPackagesForInstallation();
        foreach ($installPackages as $pkgHandle => $dir) {
            $pkg = $packageService->getClass($pkgHandle);

            // install packages via the PackageService, so the metadata is stored
            // propperly in the application/config/database.php file
            $packageService->install($pkg, array());
        }
    }
    
    /**
     * Test get package by handle
     * 
     * @dataProvider dataProviderForTestGetByHandle
     * 
     * @param string $pkgHandle
     */
    public function testGetByHandle($pkgHandle)
    {
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $packageEntity = $packageService->getByHandle($pkgHandle);
        
        $this->assertEquals($pkgHandle, $packageEntity->getPackageHandle());
    }

    public function dataProviderForTestGetByHandle(){
        return array(
            array('test_metadatadriver_annotation_default'),
            array('test_metadatadriver_annotation_legacy'),
            array('test_metadatadriver_xml'),
            array('test_metadatadriver_yaml'),
        );
    }
    
    /**
     * Test get package by id
     * 
     * @dataProvider dataProviderForTestGetByID
     * 
     * @param integer $pkgID
     */
    public function testGetByID($pkgID)
    {
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $packageEntity = $packageService->getById($pkgID);
        
        $this->assertEquals($pkgID, $packageEntity->getPackageID());
    }

    public function dataProviderForTestGetByID(){
        return array(
            array(1),
            array(2),
            array(3),
            array(4),
        );
    }
    
    /**
     * Test getInstalledList
     */
    public function testGetInstalledList()
    {
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $installedPackageEntities = $packageService->getInstalledList();
        $this->assertEquals(4, count($installedPackageEntities), 'The count of fetched package entities doesn\'t match the installed packages. ');
    }

    /**
     * Test get available packages
     * 
     * @dataProvider dataProviderForTestGetAvailablePackages
     * 
     * @param boolean $filterInstalled
     * @param integer $expectedCount
     */
    public function testGetAvailablePackages($filterInstalled, $expectedCount)
    {
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $installedPackageEntities = $packageService->getAvailablePackages($filterInstalled);
        
        $this->assertEquals($expectedCount, count($installedPackageEntities));
    }
    
    public function dataProviderForTestGetAvailablePackages()
    {
        return array(
            array(true, 1),         // select only not installed packages
            array(false, 5),        // select all packages
        );
    }
    
    
    /**
     * Test local upgradeable packages
     */
    //@todo not working yet
//    public function testGetLocalUpgradeablePackages()
//    {   
//        // Test preparation
//        self::copyTestPackageFilesForLocalUpgrade();
//        
//        
//        // Test
//        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
//        $localUpgradeablePackages = $packageService->getLocalUpgradeablePackages();
//        $this->assertEquals(1, count($localUpgradeablePackages));
//    }

    /**
     * Test getInstalledHandles
     */
    public function testGetInstalledHandles()
    {
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $installedHandles = $packageService->getInstalledHandles();
        $this->assertEquals(4, count($installedHandles));
    }

    /**
     * Test updateable Entities
     */
    public function testGetRemotelyUpgradeablePackages()
    {   
        $updateToVersion = '0.0.2';
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');

        // test preparation increase the $pkgAvailableVersion 
        // of a package from 0.0.1 to 0.0.2
        $em = \ORM::entityManager();
        $package = $packageService->getByHandle('test_metadatadriver_annotation_default');
        $package->setPackageAvailableVersion($updateToVersion);
        $em->persist($package);
        $em->flush();

        // Test the method
        $updateablePackages = $packageService->getRemotelyUpgradeablePackages();
        $this->assertEquals(1, count($updateablePackages));
    }

    
    public function testSetupLocalization()
    {
        // allready covert with tests fount under test/test/Core/Localization
    }

    public function testUninstall(Package $p)
    {
        
        // constructor (Package $p)
        
//        $p->uninstall();
//        $this->removPackageMetadataDriverFromConfig($p);
//        $config = $this->entityManager->getConfiguration();
//        $cache = $config->getMetadataCacheImpl();
//        $cache->flushAll();
    }

    public function testInstall()
    {
        // constructor (Package $p, $data)
        
//        $this->localization->pushActiveContext('system');
//        try {
//            if(!empty($p->getPackageMetadataPaths())){
//                $config = $this->entityManager->getConfiguration();
//                $driverChain = $config->getMetadataDriverImpl();
//
//                $driver = $p->getMetadataDriver();
//                $pkgNamespace = $p->getNamespace();
//
//                $driverChain->addDriver($driver, $pkgNamespace);
//                // add package entity to generated_overrides config file
//                $this->savePackageMetadataDriverToConfig($p);
//                
//                $cache = $config->getMetadataCacheImpl();
//                $cache->flushAll();
//            }
//
//            $u = new \User();
//            $swapper = new ContentSwapper();
//            $p->install($data);
//            if ($u->isSuperUser() && $swapper->allowsFullContentSwap($p) && $data['pkgDoFullContentSwap']) {
//                $swapper->swapContent($p, $data);
//            }
//            if (method_exists($p, 'on_after_swap_content')) {
//                $p->on_after_swap_content($data);
//            }
//            $this->localization->popActiveContext();
//            $pkg = $this->getByHandle($p->getPackageHandle());
//            
//            return $p;
//        } catch (\Exception $e) {
//            $this->localization->popActiveContext();
//            $error = $this->application->make('error');
//            $error->add($e);
//            return $error;
//        }
    }
    
    /**
     * Get all packages except the once in the exclude list 
     * for installation
     * 
     * @return array
     */    
    private static function getTestPackagesForInstallation()
    {
        
        // array with not installed packages
        $notInstalledPackages = array(
            'test_not_installed_package',
        );
        
        $pkgSource = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'packages';
        $packages = array();

        $filesystem = new Filesystem();
        foreach ($filesystem->directories($pkgSource) as $dir) {
            // if package matches the exclude array skip it
            if(in_array(basename($dir), $notInstalledPackages)){
                continue;
            }
            $packages[basename($dir)] = $dir;
        }
        return $packages;
    }
    
    
    /**
     * Get all packages
     * Borrowed from Antti Hukkanen <antti.hukkanen@mainiotech.fi>
     * 
     * @return array
     */    
    private static function getTestPackagesForCopy()
    {
        $pkgSource = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'packages';
        $packages = array();

        $filesystem = new Filesystem();
        foreach ($filesystem->directories($pkgSource) as $dir) {
            $packages[basename($dir)] = $dir;
        }
        return $packages;
    }
    
    /**
     * Copies the controller with an increase package version 
     * into the specific package
     */
    private static function copyTestPackageFilesForLocalUpgrade(){
        
        $pkgFolder = 'test_metadatadriver_annotation_default';
        
        $pkgSource = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'package_upgrades' .DIRECTORY_SEPARATOR .$pkgFolder;
        $target = DIR_PACKAGES . DIRECTORY_SEPARATOR . $pkgFolder. DIRECTORY_SEPARATOR .'controller.php';
        $filesystem = new Filesystem();
        $files = $filesystem->files($pkgSource);
        $filesystem->copy($files[0], $target);
    }
    
    /**
     * Delete all the temporary package folders from the packages directory
     * after all tests have run.
     * Borrowed from Antti Hukkanen <antti.hukkanen@mainiotech.fi>
     */
    public static function tearDownAfterClass()
    {
        $installPackages = self::getTestPackagesForCopy();

        $filesystem = new Filesystem();
        foreach ($installPackages as $pkg => $dir) {
            $target = DIR_PACKAGES . DIRECTORY_SEPARATOR . $pkg;
            $filesystem->deleteDirectory($target);
        }

        parent::tearDownAfterClass();
    }
}
