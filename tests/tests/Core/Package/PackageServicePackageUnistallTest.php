<?php

namespace Concrete\Tests\Core\Package;

use Illuminate\Filesystem\Filesystem;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Application;

/**
 * PackageServicePackageUnistallTest
 *
 * Test if entity proxies and metadata is deleted when uninstalling packages
 * Attention: Because the test uses a big amount of database tables, it's heavy on resources and runs very slow
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group package_tests
 */
class PackageServicePackageUnistallTest extends \ConcreteDatabaseTestCase
{
    protected $metadatas = array(
        'Concrete\Core\Entity\Package',                             // used for install tests
        'Concrete\Core\Entity\Attribute\Category',                  // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Set',                       // used for uninstall tests
        'Concrete\Core\Entity\Attribute\SetKey',                    // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Type',                      // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Key\Key',                   // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Key\ExpressKey',            // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Key\FileKey',               // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Key\LegacyKey',             // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Key\PageKey',               // used for uninstall tests
        'Concrete\Core\Entity\Attribute\Key\UserKey',               // used for uninstall tests
        'Concrete\Core\Entity\Block\BlockType\BlockType'            // used for uninstall tests

    );

    /**
     * These tables are required to test the unistall process
     *
     * @var array
     */
    protected $tables = array(
        'AuthenticationTypes',
        'BlockTypeSets',
        'BlockTypes',
        'ConfigStore',
        'ConversationEditors',
        'ConversationRatingTypes',
        'FileStorageLocationTypes',
        'Features',
        'FeatureCategories',
        'GatheringItemTemplateTypes',
        'GatheringItemTemplates',
        'GatheringDataSources',
        'GroupSets',
        'Groups',
        'Jobs',
        'JobSets',
        'MailImporters',
        'Pages',
        'PageTypes',
        'PageTypePublishTargetTypes',
        'PageTypeComposerControlTypes',
        'PageThemes',
        'PageTemplates',
        'PermissionKeys',
        'PermissionKeyCategories',
        'PermissionAccessEntityTypes',
        'SystemAntispamLibraries',
        'SystemCaptchaLibraries',
        'SystemContentEditorSnippets',
        'SystemDatabaseQueryLog',
        'TreeTypes',
        'TreeNodeTypes',
        'UserPointActions',
        'WorkflowProgressCategories',
        'WorkflowTypes',
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
     * Test uninstall packages
     */
    public function testUninstall()
    {
        // Prepare the test - unistall the first package
        $pkgHandle = 'test_metadatadriver_annotation_default';
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');
        $p = $packageService->getClass($pkgHandle);
        $packageService->uninstall($p);

        // Load the proxies
        $packageEntityManager = $p->getPackageEntityManager();
        $entityManagerConfig = $packageEntityManager->getConfiguration();
        $proxyGenerator = new \Doctrine\Common\Proxy\ProxyGenerator($entityManagerConfig->getProxyDir(), $entityManagerConfig->getProxyNamespace());

        // Test if mapping info was removed from the config file
        $classes = $packageEntityManager->getMetadataFactory()->getAllMetadata();
        foreach ($classes as $class) {
            $proxyFileName = $proxyGenerator->getProxyFileName($class->getName(), $entityManagerConfig->getProxyDir());
            $this->assertFileNotExists($proxyFileName, 'Proxy file of class ' . $class->getName() . ' still exists.');
        }

        // Test if metadata was removed from conifg
        $config = $packageService->getFileConfigORMMetadata();
        $packageMetadata = $config->get(CONFIG_ORM_METADATA_ANNOTATION_DEFAULT);
        $this->assertArrayNotHasKey($pkgHandle, $packageMetadata, 'Metadata for package '. $pkgHandle .' was found.');
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
            'test_metadatadriver_additional_namespace',
            'test_metadatadriver_annotation_default_core_extension',
            'test_metadatadriver_annotation_legacy',
            'test_metadatadriver_xml',
            'test_metadatadriver_yaml',
            'test_not_installed_package',
            'test_package_with_no_entities',
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
     * Delete all proxies
     */
    protected function deleteAllProxies(){
        $config = $this->app->make('config');
        $proxyDir = $config->get('database.proxy_classes');

        $filesystem = new Filesystem();

        foreach($filesystem->allFiles($proxyDir) as $proxyFile){
            $filesystem->delete($proxyFile);
        }
    }

    /**
     * Uninstall all packages
     */
    public function tearDown()
    {
        $packageService = $this->app->make('Concrete\Core\Package\PackageService');

        // Remove metadatadriver settings
        $config = $packageService->getFileConfigORMMetadata();
        $config->save('database.metadatadriver', array());

        $this->deleteAllProxies();

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
