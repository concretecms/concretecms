<?php
namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Database\EntityManagerFactory;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Package\ItemCategory\ItemInterface;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Support\Facade\DatabaseORM;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

abstract class Package implements LocalizablePackageInterface
{

    protected $DIR_PACKAGES_CORE = DIR_PACKAGES_CORE;
    protected $DIR_PACKAGES = DIR_PACKAGES;
    protected $REL_DIR_PACKAGES_CORE = REL_DIR_PACKAGES_CORE;
    protected $REL_DIR_PACKAGES = REL_DIR_PACKAGES;

    /**
     * @var \Concrete\Core\Entity\Package
     */
    protected $entity;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Config\Repository\Liaison
     */
    protected $config;
    
    /**
     * @var \Concrete\Core\Config\Repository\Liaison
     */
    protected $fileConfig;
    
    /**
     * Whether to automatically map core extensions into the packages src/Concrete directory
     * @var bool
     */
    protected $pkgAutoloaderMapCoreExtensions = false;

    /**
     * Array of namespace -> location autoloader entries for the package. Will automatically
     * be added to the class loader.
     * @var array
     */
    protected $pkgAutoloaderRegistries = array();

    protected $appVersionRequired = '5.7.0';
    
    protected $pkgAllowsFullContentSwap = false;

    protected $pkgContentProvidesFileThumbnails = false;

    const E_PACKAGE_NOT_FOUND = 1;
    const E_PACKAGE_INSTALLED = 2;
    const E_PACKAGE_VERSION = 3;
    const E_PACKAGE_DOWNLOAD = 4;
    const E_PACKAGE_SAVE = 5;
    const E_PACKAGE_UNZIP = 6;
    const E_PACKAGE_INSTALL = 7;
    const E_PACKAGE_MIGRATE_BACKUP = 8;
    const E_PACKAGE_INVALID_APP_VERSION = 20;
    const E_PACKAGE_THEME_ACTIVE = 21;

    /**
     * @var \Concrete\Core\Database\DatabaseStructureManager
     */
    protected $databaseStructureManager;
    
    
    const PACKAGE_METADATADRIVER_ANNOTATION = 1;
    const PACKAGE_METADATADRIVER_XML = 2;
    const PACKAGE_METADATADRIVER_YAML = 3;
    
    /**
     * @var $metadataDriver default is annotaion driver
     */
    protected $metadataDriver = self::PACKAGE_METADATADRIVER_ANNOTATION;

    /**
     * @return \Concrete\Core\Entity\Package
     */
    public function getPackageEntity()
    {
        if (!isset($this->entity)) {
            $this->entity = $this->app->make('Concrete\Core\Package\PackageService')->getByHandle($this->getPackageHandle());
        }
        return $this->entity;
    }

    public function setPackageEntity(\Concrete\Core\Entity\Package $entity)
    {
        $this->entity = $entity;
    }

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return bool
     */
    public function providesCoreExtensionAutoloaderMapping()
    {
        return $this->pkgAutoloaderMapCoreExtensions;
    }

    /**
     * Get the standard database config liaison.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public function getConfig()
    {
        return $this->getDatabaseConfig();
    }

    /**
     * Get the standard database config liaison.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public function getDatabaseConfig()
    {
        if (!$this->config) {
            $this->config = new Liaison($this->app->make('config/database'), $this->getPackageHandle());
        }

        return $this->config;
    }

    /**
     * Get the standard filesystem config liaison.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public function getFileConfig()
    {
        if (!$this->fileConfig) {
            $this->fileConfig = new Liaison($this->app->make('config'), $this->getPackageHandle());
        }

        return $this->fileConfig;
    }
    
    /**
     * Returns custom autoloader prefixes registered by the class loader.
     * @return array Keys represent the namespace, not relative to the package's namespace. Values are the path, and are relative to the package directory.
     */
    public function getPackageAutoloaderRegistries()
    {
        return $this->pkgAutoloaderRegistries;
    }

    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * Returns the translated name of the package.
     *
     * @return string
     */
    public function getPackageName()
    {
        return t($this->pkgName);
    }

    /**
     * Returns the translated package description.
     *
     * @return string
     */
    public function getPackageDescription()
    {
        return t($this->pkgDescription);
    }

    /**
     * Returns the installed package version.
     *
     * @return string
     */
    public function getPackageVersion()
    {
        return $this->pkgVersion;
    }

    /**
     * Returns the version of concrete5 required by the package.
     *
     * @return string
     */
    public function getApplicationVersionRequired()
    {
        return $this->appVersionRequired;
    }

    /**
     * Returns true if the package has an install options screen.
     *
     * @return bool
     */
    public function showInstallOptionsScreen()
    {
        return $this->hasInstallNotes() || $this->allowsFullContentSwap();
    }

    public function hasInstallNotes()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install.php');
    }

    public function hasUninstallNotes()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/uninstall.php');
    }

    /**
     * Returns true if the package has a post install screen.
     *
     * @return bool
     */
    public function hasInstallPostScreen()
    {
        return file_exists(
            $this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install_post.php');
    }

    public function allowsFullContentSwap()
    {
        return $this->pkgAllowsFullContentSwap;
    }

    public function getPackagePath()
    {
        $dirp = (is_dir(
            $this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->DIR_PACKAGES : $this->DIR_PACKAGES_CORE;
        $path = $dirp . '/' . $this->getPackageHandle();

        return $path;
    }

    /**
     * Returns the path to the package's folder, relative to the install path.
     *
     * @return string
     */
    public function getRelativePath()
    {
        $dirp = (is_dir(
            $this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? $this->REL_DIR_PACKAGES : $this->REL_DIR_PACKAGES_CORE;

        return $dirp . '/' . $this->pkgHandle;
    }


    public function getTranslationFile($locale)
    {
        $path = $this->getPackagePath() . '/' . DIRNAME_LANGUAGES;
        $languageFile = "$path/$locale/LC_MESSAGES/messages.mo";
        return $languageFile;
    }

    /**
     * Returns a path to where the packages files are located.
     *
     * @return string $path
     */
    public function contentProvidesFileThumbnails()
    {
        return $this->pkgContentProvidesFileThumbnails;
    }

    /**
     * Installs the package info row and installs the database. Packages installing additional content should override this method, call the parent method,
     * and use the resulting package object for further installs.
     *
     * @return Package
     */
    public function install()
    {
        PackageList::refreshCache();
        $em = \Database::connection()->getEntityManager();
        $package = new \Concrete\Core\Entity\Package();
        $package->setPackageName($this->getPackageName());
        $package->setPackageDescription($this->getPackageDescription());
        $package->setPackageVersion($this->getPackageVersion());
        $package->setPackageHandle($this->getPackageHandle());
        $em->persist($package);
        $em->flush();

        ClassLoader::getInstance()->registerPackage($this);
        $this->installDatabase();

        $env = \Environment::get();
        $env->clearOverrideCache();
        \Localization::clearCache();

        return $package;
    }


    public function uninstall()
    {
        $manager = new Manager($this->app);
        $categories = $manager->getPackageItemCategories();
        $package = $this->getPackageEntity();
        foreach($categories as $category) {
            if ($category->hasItems($package)) {
                $category->removeItems($package);
            }
        }

        \Config::clearNamespace($this->getPackageHandle());
        $this->app->make('config/database')->clearNamespace($this->getPackageHandle());
        
        if(!empty($this->getPackageMetadataPaths())){
            $this->destroyProxyClasses($this->getPackageEntityManager());
        }

        $em = \ORM::entityManager();
        $em->remove($package);
        $em->flush();

        \Localization::clearCache();
    }
    /**
     * Gets the contents of the package's CHANGELOG file. If no changelog is available an empty string is returned.
     *
     * @return string
     */
    public function getChangelogContents()
    {
        if (file_exists($this->getPackagePath() . '/CHANGELOG')) {
            $contents = Core::make('helper/file')->getContents($this->getPackagePath() . '/CHANGELOG');

            return nl2br(Core::make('helper/text')->entities($contents));
        }

        return '';
    }

    /**
     * @deprecated
     */
    public static function getInstalledList()
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getInstalledList();
    }

    /**
     * @deprecated
     */
    public static function getByHandle($pkgHandle)
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getByHandle($pkgHandle);
    }


    /**
     * @deprecated
     */
    public static function getLocalUpgradeablePackages()
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getLocalUpgradeablePackages();
    }

    /**
     * @deprecated
     */
    public static function getRemotelyUpgradeablePackages()
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getRemotelyUpgradeablePackages();
    }

    /**
     * @deprecated
     */
    public static function getAvailablePackages()
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getAvailablePackages();
    }

    /**
     * @deprecated
     */
    public static function getByID($pkgID)
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getByID($pkgID);
    }

    /**
     * @deprecated
     */
    public static function getClass($pkgHandle)
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getClass($pkgHandle);
    }



    /**
     * This is the pre-test routine that packages run through before they are installed. Any errors that come here are
     * to be returned in the form of an array so we can show the user. If it's all good we return true.
     *
     * @param string $package Package handle
     * @param bool $testForAlreadyInstalled
     *
     * @return array|bool Returns an array of errors or true if the package can be installed
     */
    public function testForInstall($testForAlreadyInstalled = true)
    {
        $errors = array();

        // Step 1 does that package exist ?
        if ((!is_dir(DIR_PACKAGES . '/' . $this->getPackageHandle()) && (!is_dir(
                    DIR_PACKAGES_CORE . '/' . $this->getPackageHandle()))) || $this->getPackageHandle() == ''
        ) {
            $errors[] = self::E_PACKAGE_NOT_FOUND;
        } elseif ($this instanceof BrokenPackage) {
            $errors[] = self::E_PACKAGE_NOT_FOUND;
        }

        // Step 2 - check to see if the user has already installed a package w/this handle
        if ($testForAlreadyInstalled) {
            $entity = $this->getPackageEntity();
            if (is_object($entity) && $entity->isPackageInstalled()) {
                $errors[] = self::E_PACKAGE_INSTALLED;
            }
        }

        if (count($errors) == 0) {
            // test minimum application version requirement
            if (version_compare(APP_VERSION, $this->getApplicationVersionRequired(), '<')) {
                $errors[] = array(self::E_PACKAGE_VERSION, $this->getApplicationVersionRequired());
            }
        }

        if (count($errors) > 0) {
            $e = $this->app->make('error');
            foreach($errors as $error) {
                $e->add($this->getErrorText($error));
            }
            return $e;
        } else {
            return true;
        }
    }

    protected function getErrorText($result)
    {
        $errorText = array(
            self::E_PACKAGE_INSTALLED => t("You've already installed that package."),
            self::E_PACKAGE_NOT_FOUND => t("Invalid Package."),
            self::E_PACKAGE_VERSION => t("This package requires concrete5 version %s or greater."),
            self::E_PACKAGE_DOWNLOAD => t("An error occurred while downloading the package."),
            self::E_PACKAGE_SAVE => t("concrete5 was not able to save the package after download."),
            self::E_PACKAGE_UNZIP => t('An error occurred while trying to unzip the package.'),
            self::E_PACKAGE_INSTALL => t('An error occurred while trying to install the package.'),
            self::E_PACKAGE_MIGRATE_BACKUP => t(
                'Unable to backup old package directory to %s',
                \Config::get('concrete.misc.package_backup_directory')
            ),
            self::E_PACKAGE_INVALID_APP_VERSION => t(
                'This package isn\'t currently available for this version of concrete5. Please contact the maintainer of this package for assistance.'
            ),
            self::E_PACKAGE_THEME_ACTIVE => t('This package contains the active site theme, please change the theme before uninstalling.'),
        );

        $testResultsText = array();
        if (is_array($result)) {
            $et = $errorText[$result[0]];
            array_shift($result);
            $testResultsText = vsprintf($et, $result);
        } elseif (is_int($result)) {
            $testResultsText = $errorText[$result];
        } elseif (!empty($result)) {
            $testResultsText = $result;
        }

        return $testResultsText;
    }

    /**
     * @return bool|int[] true on success, array of error codes on failure
     */
    public function testForUninstall()
    {
        $errors = array();
        $manager = new Manager($this->app);

        /**
         * @var $driver ItemInterface
         */
        $driver = $manager->driver('theme');
        $themes = $driver->getItems($this->getPackageEntity());
        /** @var Theme[] $themes */

        // Step 1, check for active themes
        $active_theme = Theme::getSiteTheme();
        foreach ($themes as $theme) {
            if ($active_theme->getThemeID() == $theme->getThemeID()) {
                $errors[] = self::E_PACKAGE_THEME_ACTIVE;
                break;
            }
        }

        if (count($errors) > 0) {
            $e = $this->app->make('error');
            foreach($errors as $error) {
                $e->add($this->getErrorText($error));
            }
            return $e;
        } else {
            return true;
        }
    }

    /**
     * Moves the current package's directory to the trash directory renamed with the package handle and a date code.
     */
    public function backup()
    {
        // you can only backup root level packages.
        // Need to figure something else out for core level
        if ($this->getPackageHandle() != '' && is_dir(DIR_PACKAGES . '/' . $this->getPackageHandle())) {
            $trash = \Config::get('concrete.misc.package_backup_directory');
            if (!is_dir($trash)) {
                mkdir($trash, \Config::get('concrete.filesystem.permissions.directory'));
            }
            $trashName = $trash . '/' . $this->getPackageHandle() . '_' . date('YmdHis');
            $ret = rename(DIR_PACKAGES . '/' . $this->getPackageHandle(), $trashName);
            if (!$ret) {
                $e = \Core::make('error');
                $e->add($this->getErrorText(self::E_PACKAGE_MIGRATE_BACKUP));
                return $e;
            } else {
                $this->backedUpFname = $trashName;
            }
        }
    }

    /**
     * If a package was just backed up by this instance of the package object and the packages/package handle directory doesn't exist, this will restore the
     * package from the trash.
     */
    public function restore()
    {
        if (strlen($this->backedUpFname) && is_dir($this->backedUpFname) && !is_dir(DIR_PACKAGES . '/' . $this->getPackageHandle())) {
            return @rename($this->backedUpFname, DIR_PACKAGES . '/' . $this->pkgHandle);
        }

        return false;
    }

    /**
     * Returns an array of paths directory containing package entities.
     *
     * @return string
     */
    public function getPackageEntityPaths()
    {
        // Support for the legacy method for backwards compatibility
        if (method_exists($this, 'getPackageEntityPath')) {
            return array($this->getPackageEntityPath());
        }
        return array($this->getPackagePath() . '/' . DIRNAME_CLASSES);
    }

    /**
     * Installs the packages database through doctrine entities and db.xml
     * database definitions.
     */
    public function installDatabase()
    {
        $this->installEntitiesDatabase();

        static::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
    }

    public function installEntitiesDatabase()
    {   
        // if the src folder doesn't exist, we assume, that no entities are present.
        if(empty($this->getPackageMetadataPaths())){
            return;
        }
        
        $em = $this->getPackageEntityManager();
        
        // Update database
        $structure = new DatabaseStructureManager($em);
        $structure->installDatabase();
        
        // Create or update entity proxies
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $em->getProxyFactory()->generateProxyClasses($metadata, $em->getConfiguration()->getProxyDir());
    }
    
    /**
     * Installs a package's database from an XML file.
     *
     * @param string $xmlFile Path to the database XML file
     *
     * @return bool|\stdClass Returns false if the XML file could not be found
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public static function installDB($xmlFile)
    {
        if (!file_exists($xmlFile)) {
            return false;
        }

        $db = \Database::connection();
        $db->beginTransaction();

        $parser = Schema::getSchemaParser(simplexml_load_file($xmlFile));
        $parser->setIgnoreExistingTables(false);
        $toSchema = $parser->parse($db);

        $fromSchema = $db->getSchemaManager()->createSchema();
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $saveQueries = $schemaDiff->toSaveSql($db->getDatabasePlatform());

        foreach ($saveQueries as $query) {
            $db->query($query);
        }

        $db->commit();

        $result = new \stdClass();
        $result->result = false;

        return $result;
    }

    /**
     * Updates a package's name, description, version and ID using the current class properties.
     */
    public function upgradeCoreData()
    {
        $em = \ORM::entityManager();
        $entity = $this->getPackageEntity();
        if (is_object($entity)) {
            $entity->setPackageName($this->getPackageName());
            $entity->setPackageDescription($this->getPackageDescription());
            $entity->setPackageVersion($this->getPackageVersion());
            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * Upgrades a package's database and refreshes all blocks.
     */
    public function upgrade()
    {
        $this->upgradeDatabase();

        // now we refresh all blocks
        $manager = new Manager($this->app);
        $items = $manager->driver('block')->getItems($this->getPackageEntity());
        foreach($items as $item) {
            $item->refresh();
        }

        \Localization::clearCache();
    }

    /**
     * Updates a package's database using entities and a db.xml.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function upgradeDatabase()
    {
        $this->destroyProxyClasses($this->getPackageEntityManager());
        $this->installEntitiesDatabase();

        static::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
    }

    /**
     * Get the metadatadriver type for the package
     * 
     * @return integer
     */
    public function getMetadataDriverType()
    {
        return $this->metadataDriver;
    }

    /**
     * Create the appropriate ORM metadata driver
     * 
     * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
     *          returns eather a AnnotationDriver or a FileDriver
     */
    public function getMetadataDriver()
    {
        if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_ANNOTATION){
            if(version_compare($this->getApplicationVersionRequired(), '5.8.0', '<')){
                // Legacy - uses SimpleAnnotationReader
                $cachedSimpleAnnotationReader = $this->app->make('orm/cachedSimpleAnnotationReader');
                $simpleAnnotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($cachedSimpleAnnotationReader, $this->getPackageMetadataPaths());
                return $simpleAnnotationDriver;
            }else{
                // Use default AnnotationReader
                $cachedAnnotationReader = $this->app->make('orm/cachedAnnotationReader');
                $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($cachedAnnotationReader, $this->getPackageMetadataPaths());
                return $annotationDriver;
            }
        } else if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_XML){
            $driverImpl = new \Doctrine\ORM\Mapping\Driver\XmlDriver($this->getPackageMetadataPaths());

        } else if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_YAML){
            $driverImpl = new \Doctrine\ORM\Mapping\Driver\YamlDriver($this->getPackageMetadataPaths());
        }
        return $driverImpl;
    }
    
    /**
     * Get path to the location containing the metadata info
     * 
     * @return array 
     */
    public function getPackageMetadataPaths()
    {
        // annotations entity path
        if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_ANNOTATION){
            // Support for the legacy method for backwards compatibility
            if (method_exists($this, 'getPackageEntityPath')) {
                $paths = array($this->getPackageEntityPath());
            }else{
                $paths = array($this->getPackagePath() . '/' . DIRNAME_CLASSES);
            }
        } else if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_XML){
            // return xml metadata dir
            $paths =  array($this->getPackagePath() . DIRECTORY_SEPARATOR . REL_DIR_METADATA_XML);
        } else if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_YAML){
            // return yaml metadata dir
            $paths =  array($this->getPackagePath() . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML);
        }
        // Check if paths exists and is a directory
        if(!is_dir($paths[0])){
            $paths = array();
        }
        return $paths;
    }
    
    /**
     * Get the namespace of the package by the package handle
     * 
     * @param boolean $withLeadingBacksalsh
     * @return string
     */
    public function getNamespace($withLeadingBacksalsh = false)
    {   
        $leadingBkslsh = '';
        if($withLeadingBacksalsh){
            $leadingBkslsh = '\\';
        }
        return $leadingBkslsh . 'Concrete\\Package\\' . camelcase($this->getPackageHandle());
    }
    
        
    /**
     * Create a entity manager used for the package installation, 
     * update and unistall process.
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getPackageEntityManager()
    {
        $config = Setup::createConfiguration(true, $this->app->make('config')->get('database.proxy_classes'));
        
        // Create a temporary EntityManager with the apropriate metadata driver 
        // for the package installation
        // We don't want to accidentially update other packages, so we create
        // a new EntityManager which contains only the ORM metadata of the specific package
        if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_ANNOTATION){
            if(version_compare($this->getApplicationVersionRequired(), '5.8.0', '<')){
                // Legacy - uses SimpleAnnotationReader
                $driverImpl = $config->newDefaultAnnotationDriver($this->getPackageMetadataPaths());
            }else{
                // Use default AnnotationReader
                $driverImpl = $config->newDefaultAnnotationDriver($this->getPackageMetadataPaths(), false);
            }
        } else if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_XML){
            $driverImpl = new \Doctrine\ORM\Mapping\Driver\XmlDriver($this->getPackageMetadataPaths());

        } else if ($this->metadataDriver === self::PACKAGE_METADATADRIVER_YAML){
            $driverImpl = new \Doctrine\ORM\Mapping\Driver\YamlDriver($this->getPackageMetadataPaths());
        }
        $config->setMetadataDriverImpl($driverImpl);
        $em = EntityManager::create(\Database::connection(), $config);
        return $em;
    }
    
    /**
     * Destroys all proxies related to a package 
     */
    protected function destroyProxyClasses(\Doctrine\ORM\EntityManager $em)
    {
        $config = $em->getConfiguration();
        $proxyGenerator = new \Doctrine\Common\Proxy\ProxyGenerator($config->getProxyDir(), $config->getProxyNamespace());
        
        $classes = $em->getMetadataFactory()->getAllMetadata();
        foreach ($classes as $class) {

            $proxyFileName = $proxyGenerator->getProxyFileName($class->getName(), $config->getProxyDir());
            if(file_exists($proxyFileName)){
                @unlink($proxyFileName);
            }
        }
    }
    
    /**
     * @deprecated
     */
    public function getEntityManager()
    {
        return \ORM::entityManager();
    }
}
