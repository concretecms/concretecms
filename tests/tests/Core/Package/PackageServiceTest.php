<?php

namespace Concrete\Tests\Core\Package;

use Illuminate\Filesystem\Filesystem;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Application;

/**
 * PackageServiceTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 * @group package_tests
 */
class PackageServiceTest extends \ConcreteDatabaseTestCase
{
    protected $metadatas = array(
        'Concrete\Core\Entity\Package',                             // used for install tests
    );

    private $totallyInstalledBackages = 7;

//    /**
//     * These tables are required to test the unistall process
//     *
//     * @var array
//     */
//    protected $tables = array(
//        'SystemAntispamLibraries',  // used for uninstall tests
//        'AuthenticationTypes',      // used for uninstall tests
//        'BlockTypeSets',            // used for uninstall tests
//        'SystemCaptchaLibraries',   // used for uninstall tests
//    );
    
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
        $this->assertEquals($this->totallyInstalledBackages, count($installedPackageEntities), 'The count of fetched package entities doesn\'t match the installed packages. ');
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
            array(false, ($this->totallyInstalledBackages + 1 )), // select all packages instlled + 1
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
        $this->assertEquals(($this->totallyInstalledBackages), count($installedHandles));
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
        // allready covert with tests found under test/test/Core/Localization
    }

    /**
     * Test uninstall packages
     */
    public function testUninstall()
    {
        // This thest is covered in Concrete\Tests\Core\Package\PackageServicePackageUnistallTest
    }
    
    /**
     * Test package installation
     * 
     * Test the following things
     * - proxy creation
     * - storing of the package metadata in the config file
     * - test for the corret namespace in config file
     * - check if the correct metadata paths are stored in the config file
     *
     * @todo This test doesn't covert the funtionality related to ContentSwapper
     *       and on_after_swap_content() method
     * 
     * @dataProvider dataProviderTestInstall
     */
    public function testInstall($pkgHandle, $conifgPath, $namespaces, $namepacesCount)
    {   
        
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $p = $packageService->getClass($pkgHandle);
        
        // Load the proxies and test if they were created
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
        $driverSetConfigs = $packageMetadata[$pkgHandle];
        $this->assertEquals($namepacesCount, count($driverSetConfigs));

        foreach($driverSetConfigs as $key => $driverSetConfig){

            // Test if the namespace matches
            $this->assertEquals($namespaces[$key]['namespace'], $driverSetConfig['namespace'], 'Namespace does n\'t match.');

            // Test if the paths count and path matches;
            $this->assertEquals($namespaces[$key]['numberOfPaths'], count($driverSetConfig['paths']), 'Path count does n\'t match.');

            // Test src path
            foreach($driverSetConfig['paths'] as $k => $path){
                $this->assertEquals($namespaces[$key]['metadataPaths'][$k], $path, 'Path does n\'t match.');
            }
        }
    }
    
    public function dataProviderTestInstall()
    {
        return array(
            // Default AnnotationDriver
            array(
                'pkgHandle' => 'test_metadatadriver_annotation_default',
                'configPath' => CONFIG_ORM_METADATA_ANNOTATION_DEFAULT,
                'namespaces' => array(
                    array(
                        'namespace' => 'Concrete\\Package\\TestMetadatadriverAnnotationDefault',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_annotation_default\\src\\Entity',
                        ),
                        'numberOfPaths' => 1,
                    ),
                ),
                'namepacesCount' => 1,
            ),
            // Legacy AnnotationDriver
            array(
                'pkgHandle' => 'test_metadatadriver_annotation_legacy',
                'configPath' => CONFIG_ORM_METADATA_ANNOTATION_LEGACY,
                'namespaces' => array(
                    array(
                        'namespace' => 'Concrete\\Package\\TestMetadatadriverAnnotationLegacy',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_annotation_legacy\\src',
                        ),
                        'numberOfPaths' => 1,
                    ),
                ),
                'namepacesCount' => 1,
            ),
            // Xml AnnotationDriver
            array(
                'pkgHandle' => 'test_metadatadriver_xml',
                'configPath' => CONFIG_ORM_METADATA_XML,
                'namespaces' => array(
                    array(
                        'namespace' => 'Concrete\\Package\\TestMetadatadriverXml',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_xml\\config\\xml',
                        ),
                        'numberOfPaths' => 1,
                    ),
                ),
                'namepacesCount' => 1,
            ),
            // Yaml AnnotationDriver
            array(
                'pkgHandle' => 'test_metadatadriver_yaml',
                'configPath' => CONFIG_ORM_METADATA_YAML,
                'namespaces' => array(
                    array(
                        'namespace' => 'Concrete\\Package\\TestMetadatadriverYaml',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_yaml\\config\\yaml',
                        ),
                        'numberOfPaths' => 1,
                    ),
                ),
                'namepacesCount' => 1,
            ),
            // Default AnnotationDriver with coreextension eneabled
            array(
                'pkgHandle' => 'test_metadatadriver_annotation_default_core_extension',
                'configPath' => CONFIG_ORM_METADATA_ANNOTATION_DEFAULT,
                'namespaces' => array(
                    array(
                        'namespace' => 'Concrete\\Package\\TestMetadatadriverAnnotationDefaultCoreExtension',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_annotation_default_core_extension\\src\\Entity',
                            '\\packages\\test_metadatadriver_annotation_default_core_extension\\src\\Concrete',
                        ),
                        'numberOfPaths' => 2,
                    ),
                ),
                'namepacesCount' => 1,
            ),
            // Default AnnotationDriver with additional namespaces registerd
            array(
                'pkgHandle' => 'test_metadatadriver_additional_namespace',
                'configPath' => CONFIG_ORM_METADATA_ANNOTATION_DEFAULT,
                'namespaces' => array(
                    array(
                        'namespace' => 'Concrete\\Package\\TestMetadatadriverAdditionalNamespace',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_additional_namespace\\src\\Entity',
                        ),
                        'numberOfPaths' => 1,
                    ),
                    array(
                        'namespace' => 'PortlandLabs\\Concrete5\\MigrationTool',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_additional_namespace\\src\\PortlandLabs\\Concrete5\\MigrationTool',
                        ),
                        'numberOfPaths' => 1,
                    ),
                    array(
                        'namespace' => 'Dummy',
                        'metadataPaths' => array(
                            '\\packages\\test_metadatadriver_additional_namespace\\src\\Dummy',
                        ),
                        'numberOfPaths' => 1,
                    ),
                ),
                'namepacesCount' => 3,
            ),
        );
    }

    /**
     * Test if package without entites - this package shouldn't be installed safed
     * in th config file
     *
     * @dataProvider dataProviderTestInstallWithPackageWithNoEntites
     *
     * @param string $conifgPath
     */
    public function testInstallWithPackageWithNoEntities($conifgPath)
    {
        $pkgHandle = 'test_package_with_no_entites';

        $packageService = $this->app->make('Concrete\Core\Package\PackageService');

        // Test if conifg is present
        $config = $packageService->getFileConfigORMMetadata();
        $packageMetadata = $config->get($conifgPath);
        $this->assertArrayNotHasKey($pkgHandle, $packageMetadata, 'Metadata for package '. $pkgHandle .' was found.');
    }

    public function dataProviderTestInstallWithPackageWithNoEntites()
    {
        return array(
            array(CONFIG_ORM_METADATA_ANNOTATION_DEFAULT),
            array(CONFIG_ORM_METADATA_ANNOTATION_LEGACY),
            array(CONFIG_ORM_METADATA_XML),
            array(CONFIG_ORM_METADATA_YAML),
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
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');

        // Remove metadatadriver settings
        $config = $packageService->getFileConfigORMMetadata();

        $metaDriverConfig = $config->get('database');
        unset($metaDriverConfig['metadatadriver']);
        $config->save('database', $metaDriverConfig['metadatadriver']);
        
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
