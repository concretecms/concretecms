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
        'AttributeKeyCategories' // used for unistall tests
    );
    
    /**
     * These tables are required to test the unistall process
     * 
     * @var array
     */
    protected $tables = array('SystemAntispamLibraries');
    
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
            throw new \Exception("Cannot write to the packages directory for the testing purposes. Please check permissions!");
        }

        $packages = self::getTestPackagesForCopy();

        // First make sure that none of the packages already exist
        foreach ($packages as $pkg => $dir) {
            if ($filesystem->exists(DIR_PACKAGES . '/' . $pkg)) {
                throw new \Exception("A package directory for a package named ${pkg} already exists. It cannot exist prior to running these tests.");
            }
        }
        // Then, move the package folders to the package folder
        foreach ($packages as $pkg => $dir) {
            $target = DIR_PACKAGES . '/' . $pkg;
            $filesystem->copyDirectory($dir, $target);
        }
    }
    
    /**
     * Install test packages
     */
    protected function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
        $this->em = \ORM::entityManager();

        // The setUp() procedures install the database table required for
        // installing the packages. This is why we need to install these
        // AFTER that has run.
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

    public function testUninstall()
    {   
        // Prepare the test - unistall the first package
        $pkgHandle = 'test_metadatadriver_annotation_default';
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $p = $packageService->getClass($pkgHandle);
        $packageService->uninstall($p);
        
        // Load the proxies and test if they are still present
        $packageEntityManager = $p->getPackageEntityManager();
        $config = $packageEntityManager->getConfiguration();
        $proxyGenerator = new \Doctrine\Common\Proxy\ProxyGenerator($config->getProxyDir(), $config->getProxyNamespace());
        
        $classes = $packageEntityManager->getMetadataFactory()->getAllMetadata();
        foreach ($classes as $class) {

            $proxyFileName = $proxyGenerator->getProxyFileName($class->getName(), $config->getProxyDir());
            $this->assertFileNotExists($proxyFileName, 'Proxy file of class ' . $class->getName() . ' still exists.');
        }
        
        
        // Test if mapping info was removed from the config file

        // Test if the proxies were removed
  
        // Eventually test cache
    }
    
    /**
     * Test package installation
     * 
     * Test the following things
     * - proxy generation
     * - metadata storing in the config file
     * - test for the corret namespace in config file
     * - check if the correct metadata paths are stored in the config file
     * 
     * @dataProvider dataProviderTestInstall
     */
    public function testInstall($pkgHandle, $conifgPath, $namespace, $numberOfPaths)
    {   
        
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $p = $packageService->getClass($pkgHandle);
        // Install was already triggert by the setUp method
        
        // Test if mapping info was created and is correct
        
        // Load the proxies and test if they are still present
        $packageEntityManager = $p->getPackageEntityManager();
        $entityManagerConfig = $packageEntityManager->getConfiguration();
        $proxyGenerator = new \Doctrine\Common\Proxy\ProxyGenerator($entityManagerConfig->getProxyDir(), $entityManagerConfig->getProxyNamespace());
        
        $classes = $packageEntityManager->getMetadataFactory()->getAllMetadata();
        foreach ($classes as $class) {
            
            $proxyFileName = $proxyGenerator->getProxyFileName($class->getName(), $entityManagerConfig->getProxyDir());
            // Test if file exists
            $this->assertFileExists($proxyFileName, 'Proxy file of class ' . $class->getName() . ' does not exists.');
        }
        
        // Test if conifg is present
        $config = $packageService->getFileConfigORMMetadata();
        $packageMetadata = $config->get($conifgPath);
        $this->assertArrayHasKey($pkgHandle, $packageMetadata, 'Metadata for package '. $pkgHandle .' was not present');
        
        // The config should only conatain on set of namespace with paths.
        // Example:
        // 'test_metadatadriver_annotation_default' => array(
        //     array(
        //          'namespace' => 'Concrete\\Package\\TestMetadatadriverAnnotationDefault',
        //          'paths' => array(
        //              '/path/to/server/packages/test_metadatadriver_annotation_default/src'
        //          ),
        //     ),
        // ),
        $metadataPaths = $packageMetadata[$pkgHandle];
        $this->assertEquals(1, count($metadataPaths));
        // Get first driver resultset
        $driverSet = $packageMetadata[$pkgHandle][0];
        
        // Test if the namespace matches
        $this->assertEquals($namespace, $driverSet['namespace']);
        
        // Test if the paths count and path matches;
        $this->assertEquals($numberOfPaths, count($driverSet['paths']));
        
        // Test contents of metadata paths
        
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
    
    public function dataProviderTestInstall()
    {
        return array(
            array(
                'pkgHandle' => 'test_metadatadriver_annotation_default',
                'configPath' => CONFIG_ORM_METADATA_ANNOTATION_DEFAULT,
                'namespace' => 'Concrete\\Package\\TestMetadatadriverAnnotationDefault',
                'numberOfPahts' => 1,
                'metadataPaths' => array(
                    '\\packages\\test_metadatadriver_annotation_default\\src\\Entity',
                )
            ),
            array(
                'pkgHandle' => 'test_metadatadriver_annotation_legacy',
                'configPath' => CONFIG_ORM_METADATA_ANNOTATION_LEGACY,
                'namespace' => 'Concrete\\Package\\TestMetadatadriverAnnotationLegacy',
                'numberOfPahts' => 1,
                'metadataPaths' => array(
                    '\\packages\\test_metadatadriver_annotation_legacy\\src',
                )
            ),
            array(
                'pkgHandle' => 'test_metadatadriver_xml',
                'configPath' => CONFIG_ORM_METADATA_XML,
                'namespace' => 'Concrete\\Package\\TestMetadatadriverXml',
                'numberOfPahts' => 1,
                'metadataPaths' => array(
                    '\\packages\\test_metadatadriver_xml\\config\\xml',
                )
            ),
            array(
                'pkgHandle' => 'test_metadatadriver_yaml',
                'configPath' => CONFIG_ORM_METADATA_YAML,
                'namespace' => 'Concrete\\Package\\TestMetadatadriverYaml',
                'numberOfPahts' => 1,
                'metadataPaths' => array(
                    '\\packages\\test_metadatadriver_yaml\\config\\yaml',
                )
            ),
        );
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
     * Uninstall all packages
     */
    public function tearDown()
    {
        // Remove packages propperly through the packageService
//        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
//        $installPackages = self::getTestPackagesForInstallation();
//        foreach ($installPackages as $pkgHandle => $dir) {
//            $pkg = $packageService->getClass($pkgHandle);
//
//            // uninstall packages via the PackageService, so the metadata and the proxies are removed properly
//            $packageService->uninstall($pkg, array());
//        }
        
        parent::tearDown();
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
