<?php
namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Database\EntityManager\Driver\CoreDriver;
use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Package\ItemCategory\ItemInterface;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Page\Theme\Theme;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Gettext\Translations;

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
     * @deprecated
     * This will be set to FALSE in 8.1
     * Additionally, if your package requires 8.0 or greater, it will be set to false automatically
     * Whether to automatically map core extensions into the packages src/Concrete directory (and map them to Concrete\Package\MyPackage), or map the entire src/
     * directory to Concrete\Package\MyPackage\Src (and automatically map core extensions
     * to Concrete\Package\MyPackage\Src)
     *
     * @var bool
     */
    protected $pkgEnableLegacyNamespace = true;

    /**
     * Array of location -> namespace autoloader entries for the package. Will automatically
     * be added to the class loader. (e.g. array('src/PortlandLabs' => \PortlandLabs')).
     *
     * @var array
     */
    protected $pkgAutoloaderRegistries = [];

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

    public function getApplication()
    {
        return $this->app;
    }

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getContentSwapper()
    {
        return new ContentSwapper();
    }

    public function installContentFile($file)
    {
        $ci = new ContentImporter();
        $ci->importContentFile($this->getPackagePath() . '/' . $file);
    }

    /**
     * Should this pacakge enable legacy namespaces.
     *
     * This returns true IF:
     * 1. $this->pkgAutoloaderMapCoreExtensions is false or unset
     * 2. The required package version > 7.9.9 meaning version 8 or newer
     * 3. $this->pkgEnableLegacyNamespace is true
     *
     * @return bool
     */
    public function shouldEnableLegacyNamespace()
    {
        if (isset($this->pkgAutoloaderMapCoreExtensions) && $this->pkgAutoloaderMapCoreExtensions) {
            // We're no longer using this to denote non-legacy applications, but if a package uses this
            // that means it's NOT using the legacy namespace.
            return false;
        }

        $concrete5 = '7.9.9';
        $package = $this->getApplicationVersionRequired();
        if (version_compare($package, $concrete5, '>')) {
            return false;
        }

        return $this->pkgEnableLegacyNamespace;
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
     *
     * @return array Keys represent the namespace, not relative to the package's namespace. Values are the path, and are relative to the package directory
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

    /**
     * Returns the path starting from c5 installation folder to the package folder.
     *
     * @return string
     */
    public function getRelativePathFromInstallFolder()
    {
        return '/' . DIRNAME_PACKAGES . '/' . $this->getPackageHandle();
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
        foreach ($categories as $category) {
            if ($category->hasItems($package)) {
                $category->removeItems($package);
            }
        }

        \Config::clearNamespace($this->getPackageHandle());
        $this->app->make('config/database')->clearNamespace($this->getPackageHandle());

        $em = $this->getPackageEntityManager();
        if (is_object($em)) {
            $this->destroyProxyClasses($em);
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
            $contents = \Core::make('helper/file')->getContents($this->getPackagePath() . '/CHANGELOG');

            return nl2br(\Core::make('helper/text')->entities($contents));
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
    public static function getInstalledHandles()
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getInstalledHandles();
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
    public static function getAvailablePackages($filterInstalled = true)
    {
        // this should go through the facade instead
        return \Concrete\Core\Support\Facade\Package::getAvailablePackages($filterInstalled);
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
        $errors = [];

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
                $errors[] = [self::E_PACKAGE_VERSION, $this->getApplicationVersionRequired()];
            }
        }

        if (count($errors) > 0) {
            $e = $this->app->make('error');
            foreach ($errors as $error) {
                $e->add($this->getErrorText($error));
            }

            return $e;
        } else {
            return true;
        }
    }

    protected function getErrorText($result)
    {
        $errorText = [
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
        ];

        $testResultsText = [];
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
        $errors = [];
        $manager = new Manager($this->app);

        /**
         * @var ItemInterface
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
            foreach ($errors as $error) {
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
     * @deprecated
     * This method was limited. It let you specify a location but in V8 with the Doctrine Entity Manager driver chain
     * we also need to specify namespaces. Instead of specifying entity paths this way, update your
     * package controller to implement the Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface,
     * and create a method named getEntityManagerProvider that returns an instance of the
     * Concrete\Core\Database\EntityManager\Provider\ProviderInterface.
     *
     * For example, if I want to specify that my package has entities found at src/PortlandLabs\FooBar\Entity, with the
     * namespace PortlandLabs\FooBar\Entity, my method is simply
     * public function getEntityManagerProvider() {
     *     return new StandardPackageProvider($this->app, $this, ['src/MSM/Entity' => 'PortlandLabs\MSM\Entity']);
     * }
     */
    public function getPackageEntityPaths()
    {
        // Support for the legacy method for backwards compatibility
        if (method_exists($this, 'getPackageEntityPath')) {
            return [$this->getPackageEntityPath()];
        }
        // If we're using a legacy package, we scan the entire src directory
        return [$this->getPackagePath() . '/' . DIRNAME_CLASSES];
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
        $em = $this->getPackageEntityManager();
        if (is_object($em)) {
            $structure = new DatabaseStructureManager($em);
            $structure->installDatabase();

            // Create or update entity proxies
            $metadata = $em->getMetadataFactory()->getAllMetadata();
            $em->getProxyFactory()->generateProxyClasses($metadata, $em->getConfiguration()->getProxyDir());
        }
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
        $em = \Database::connection()->getEntityManager();
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
        $items = $manager->driver('block_type')->getItems($this->getPackageEntity());
        foreach ($items as $item) {
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
        $em = $this->getPackageEntityManager();
        if (is_object($em)) {
            $this->destroyProxyClasses($em);
            $this->installEntitiesDatabase();
        }
        static::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB);
    }

    /**
     * Get the namespace of the package by the package handle.
     *
     * @param bool $withLeadingBacksalsh
     *
     * @return string
     */
    public function getNamespace($withLeadingBacksalsh = false)
    {
        $leadingBkslsh = '';
        if ($withLeadingBacksalsh) {
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
    public function getPackageEntityManager()
    {
        $providerFactory = new PackageProviderFactory($this->app, $this);
        $provider = $providerFactory->getEntityManagerProvider();
        $drivers = $provider->getDrivers();
        if (count($drivers)) {
            $config = Setup::createConfiguration(true, $this->app->make('config')->get('database.proxy_classes'));
            $driverImpl = new MappingDriverChain();
            $coreDriver = new CoreDriver($this->app);

            // Add all the installed packages so that the new package could potentially extend packages that are already
            // installed
            $packages = $this->app->make(PackageService::class)->getInstalledList();
            foreach($packages as $package) {
                $existingProviderFactory = new PackageProviderFactory($this->app, $package->getController());
                $existingProvider = $existingProviderFactory->getEntityManagerProvider();
                $existingDrivers = $existingProvider->getDrivers();
                if (count($existingDrivers)) {
                    foreach($existingDrivers as $existingDriver) {
                        $driverImpl->addDriver($existingDriver->getDriver(), $existingDriver->getNamespace());
                    }
                }
            }

            // Add the core driver to it so packages can extend the core and not break.
            $driverImpl->addDriver($coreDriver->getDriver(), $coreDriver->getNamespace());

            foreach ($drivers as $driver) {
                $driverImpl->addDriver($driver->getDriver(), $driver->getNamespace());
            }
            $config->setMetadataDriverImpl($driverImpl);
            $em = EntityManager::create(\Database::connection(), $config);

            return $em;
        }
    }

    /**
     * Destroys all proxies related to a package.
     */
    protected function destroyProxyClasses(\Doctrine\ORM\EntityManagerInterface $em)
    {
        $config = $em->getConfiguration();
        $proxyGenerator = new \Doctrine\Common\Proxy\ProxyGenerator($config->getProxyDir(), $config->getProxyNamespace());

        $classes = $em->getMetadataFactory()->getAllMetadata();
        foreach ($classes as $class) {
            // We have to do this check because we include core entities in this list because without it packages that extend
            // the core will complain.
            if (strpos($class->getName(), 'Concrete\Core\Entity') === 0) {
                continue;
            }
            $proxyFileName = $proxyGenerator->getProxyFileName($class->getName(), $config->getProxyDir());
            if (file_exists($proxyFileName)) {
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

    /**
     * @deprecated
     * This should be handled by the Concrete\Core\Entity\Package object, not by this object
     */
    public function getPackageID()
    {
        // If this package is installed, we will query the database for this field.
        if ($this->getPackageEntity()) {
            return $this->getPackageEntity()->getPackageID();
        }
    }

    /**
     * Override this method in your package controller to add strings to the translator, so that you can translate dynamically generated strings.
     *
     * @param Translations $translations
     *
     * @example
     * If you add to this method these two strings:
     *
     * <code>
     * $translations->insert('', 'String without context');
     * $translations->insert('MyContext', 'String with context');
     * </code>
     *
     * Then you'll be able to translate these two strings in the Translator and write translated strings with:
     *
     * <code>
     * echo t('String without context');
     * echo tc('MyContext', 'String with context');
     * </code>
     */
    public function getTranslatableStrings(Translations $translations)
    {
    }
}
