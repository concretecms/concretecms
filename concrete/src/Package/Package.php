<?php
namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Database\EntityManager\Driver\CoreDriver;
use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Package\Dependency\DependencyChecker;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Page\Theme\Theme;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Proxy\ProxyGenerator;
use Doctrine\DBAL\Schema\Comparator as SchemaComparator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Gettext\Translations;
use Localization;
use stdClass;

abstract class Package implements LocalizablePackageInterface
{
    /**
     * Error code: Invalid Package.
     *
     * @var int
     */
    const E_PACKAGE_NOT_FOUND = 1;

    /**
     * Error code: You've already installed that package.
     *
     * @var int
     */
    const E_PACKAGE_INSTALLED = 2;

    /**
     * Error code: This package requires Concrete version %s or greater.
     *
     * @var int
     */
    const E_PACKAGE_VERSION = 3;

    /**
     * Error code: An error occurred while downloading the package.
     *
     * @var int
     */
    const E_PACKAGE_DOWNLOAD = 4;

    /**
     * Error code: Concrete was not able to save the package after download.
     *
     * @var int
     */
    const E_PACKAGE_SAVE = 5;

    /**
     * Error code: An error occurred while trying to unzip the package.
     *
     * @var int
     */
    const E_PACKAGE_UNZIP = 6;

    /**
     * Error code: An error occurred while trying to install the package.
     *
     * @var int
     */
    const E_PACKAGE_INSTALL = 7;

    /**
     * Error code: Unable to backup old package directory to %s.
     *
     * @var int
     */
    const E_PACKAGE_MIGRATE_BACKUP = 8;

    /**
     * Error code: This package isn't currently available for this version of Concrete.
     *
     * @var int
     */
    const E_PACKAGE_INVALID_APP_VERSION = 20;

    /**
     * Error code: This package contains the active site theme, please change the theme before uninstalling.
     *
     * @var int
     */
    const E_PACKAGE_THEME_ACTIVE = 21;

    /**
     * Error code: This package requires PHP version %1$s or greater (the current PHP version is %2$s).
     *
     * @var int
     */
    const E_PACKAGE_PHP_VERSION = 22;

    /**
     * Absolute path to the /concrete/packages directory.
     *
     * @var string
     */
    protected $DIR_PACKAGES_CORE = DIR_PACKAGES_CORE;

    /**
     * Absolute path to the /packages directory.
     *
     * @var string
     */
    protected $DIR_PACKAGES = DIR_PACKAGES;

    /**
     * Path to the /concrete/packages directory relative to the web root.
     *
     * @var string
     */
    protected $REL_DIR_PACKAGES_CORE = REL_DIR_PACKAGES_CORE;

    /**
     * Path to the /concrete/packages directory relative to the web root.
     *
     * @var string
     */
    protected $REL_DIR_PACKAGES = REL_DIR_PACKAGES;

    /**
     * Associated package entity.
     *
     * @var \Concrete\Core\Entity\Package|null
     */
    protected $entity;

    /**
     * The Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The database configuration liaison.
     *
     * @var Liaison|null
     */
    protected $config;

    /**
     * The file configuration liaison.
     *
     * @var Liaison|null
     */
    protected $fileConfig;

    /**
     * @deprecated
     * Whether to automatically map core extensions into the packages src/Concrete directory (and map them to Concrete\Package\MyPackage),
     * or map the entire src/ directory to Concrete\Package\MyPackage\Src
     * (and automatically map core extensions to Concrete\Package\MyPackage\Src).
     * This will be ALWAYS considered as FALSE if your package requires 8.0 or greater or if your package defines the pkgAutoloaderMapCoreExtensions property.
     *
     * @var bool
     */
    protected $pkgEnableLegacyNamespace = true;

    /**
     * The custom autoloader prefixes to be automatically added to the class loader.
     * Array keys are the locations (relative to the package directory).
     * Array values are the paths (not relative to the package namespace).
     *
     * @var array
     *
     * @example ['src/PortlandLabs' => \PortlandLabs']
     */
    protected $pkgAutoloaderRegistries = [];

    /**
     * The minimum Concrete version compatible with the package.
     * Override this value according to the minimum required version for your package.
     *
     * @var string
     */
    protected $appVersionRequired = '5.7.0';

    /**
     * The minimum PHP version compatible with the package.
     * Override this value according to the minimum required version for your package.
     *
     * @var string
     * @var string
     */
    protected $phpVersionRequired = '';

    /**
     * Override this value and set it to true if your package clears all existing website content when it's being installed.
     *
     * @var bool
     */
    protected $pkgAllowsFullContentSwap = false;

    /**
     * Override this value to add additional content swap templates.
     *
     * @var array
     */
    protected $pkgContentSwapFiles = [
        "content.xml" => "Default"
    ];

    /**
     * Override this value and set it to true if your package provides the file thumbnails.
     * If it's false, the file thumbnails are generated during the install process.
     *
     * @var bool
     */
    protected $pkgContentProvidesFileThumbnails = false;

    /**
     * The full path of the package directory moved to the trash folder.
     *
     * @var string|null
     */
    protected $backedUpFname;

    /**
     * An array describing the package dependencies.
     * Keys are package handles.
     * Values may be:
     * - false: this package can't be installed if the other package is already installed.
     * - true: this package can't be installed of the other package is not installed
     * - a string: this package can't be installed of the other package is not installed or it's installed with an older version
     * - an array with two strings, representing the minimum and the maximum version of the other package to be installed.
     *
     * @var array
     *
     * @example [
     *     // This package can't be installed if a package with handle other_package_1 is already installed.
     *     'other_package_1' => false,
     *     // This package can't be installed if a package with handle other_package_2 is not installed.
     *     'other_package_2' => true,
     *     // This package can't be installed if a package with handle other_package_3 is not installed, or it has a version prior to 1.0
     *     'other_package_3' => '1.0',
     *     // This package can't be installed if a package with handle other_package_4 is not installed, or it has a version prior to 2.0, or it has a version after 2.9
     *     'other_package_4' => ['2.0', '2.9'],
     * ]
     */
    protected $packageDependencies = [];

    /**
     * Initialize the instance.
     *
     * @param Application $app the application instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the associated package entity (if available).
     *
     * @return \Concrete\Core\Entity\Package|null May return NULL if the package is invalid and/or if it's not installed
     */
    public function getPackageEntity()
    {
        if ($this->entity === null) {
            $this->entity = $this->app->make(PackageService::class)->getByHandle($this->getPackageHandle());
        }

        return $this->entity;
    }

    /**
     * Set the associated package entity.
     */
    public function setPackageEntity(PackageEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return array
     */
    public function getContentSwapFiles(): array
    {
        return $this->pkgContentSwapFiles;
    }

    /**
     * @return $this
     */
    public function setContentSwapFiles(array $pkgContentSwapFiles): Package
    {
        $this->pkgContentSwapFiles = $pkgContentSwapFiles;
        return $this;
    }

    /**
     * Get the Application instance.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Get the content swapper.
     *
     * @return \Concrete\Core\Package\ContentSwapperInterface
     */
    public function getContentSwapper()
    {
        return new ContentSwapper();
    }

    /**
     * Import a concrete5-cif XML file.
     *
     * @param string $file the path to the file, relative to the package directory
     */
    public function installContentFile($file)
    {
        $ci = new ContentImporter();
        $cache = $this->app->make('cache/request');
        $cacheEnabled = $cache->isEnabled();
        if ($cacheEnabled) {
            $cache->disable();
        }
        try {
            $ci->importContentFile($this->getPackagePath() . '/' . $file);
        } finally {
            if ($cacheEnabled) {
                $cache->enable();
            }
        }
    }

    /**
     * Should this package enable legacy namespaces?
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

        $concrete = '7.9.9';
        $package = $this->getApplicationVersionRequired();
        if (version_compare($package, $concrete, '>')) {
            return false;
        }

        return $this->pkgEnableLegacyNamespace;
    }

    /**
     * Get the default configuration liaison.
     *
     * @return Liaison
     */
    public function getConfig()
    {
        return $this->getDatabaseConfig();
    }

    /**
     * Get the database configuration liaison.
     *
     * @return Liaison
     */
    public function getDatabaseConfig()
    {
        if (!$this->config) {
            $this->config = new Liaison($this->app->make('config/database'), $this->getPackageHandle());
        }

        return $this->config;
    }

    /**
     * Get the filesystem configuration liaison.
     *
     * @return Liaison
     */
    public function getFileConfig()
    {
        if (!$this->fileConfig) {
            $this->fileConfig = new Liaison($this->app->make('config'), $this->getPackageHandle());
        }

        return $this->fileConfig;
    }

    /**
     * Get the custom autoloader prefixes to be automatically added to the class loader.
     * Array keys are the locations (relative to the package directory).
     * Array values are the paths (not relative to the package namespace).
     *
     * @return array
     *
     * @example ['src/PortlandLabs' => \PortlandLabs']
     */
    public function getPackageAutoloaderRegistries()
    {
        return $this->pkgAutoloaderRegistries;
    }

    /**
     * Get the package handle.
     *
     * @return string
     */
    public function getPackageHandle()
    {
        return isset($this->pkgHandle) ? $this->pkgHandle : '';
    }

    /**
     * Get the translated name of the package.
     *
     * @return string
     */
    public function getPackageName()
    {
        return isset($this->pkgName) ? t($this->pkgName) : '';
    }

    /**
     * Get the translated package description.
     *
     * @return string
     */
    public function getPackageDescription()
    {
        return isset($this->pkgDescription) ? t($this->pkgDescription) : '';
    }

    /**
     * Get the installed package version.
     *
     * @return string
     */
    public function getPackageVersion()
    {
        return isset($this->pkgVersion) ? $this->pkgVersion : '';
    }

    /**
     * Get the minimum Concrete version compatible with the package.
     *
     * @return string
     */
    public function getApplicationVersionRequired()
    {
        return $this->appVersionRequired;
    }

    /**
     * Get the minimum PHP version compatible with the package.
     *
     * @return string
     *
     * @example '' if the package is compatible with any PHP version that's already compatible with the core
     * @example '8' if the package requires PHP 8.0.0 and later
     * @example '8.1' if the package requires PHP 8.1.0 and later
     * @example '8.1.20' if the package requires PHP 8.1.20 and later
     */
    public function getPHPVersionRequired(): string
    {
        return $this->phpVersionRequired;
    }

    /**
     * Should the install options page be shown?
     * The install options page may be for install notes and/or full contents swap confirmation.
     *
     * @return bool
     */
    public function showInstallOptionsScreen()
    {
        return $this->hasInstallNotes() || $this->allowsFullContentSwap();
    }

    /**
     * Does this package have install notes?
     *
     * @return bool
     */
    public function hasInstallNotes()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install.php');
    }

    /**
     * Does this package have uninstall notes?
     *
     * @return bool
     */
    public function hasUninstallNotes()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/uninstall.php');
    }

    /**
     * Does this package have a post-install page?
     *
     * @return bool
     */
    public function hasInstallPostScreen()
    {
        return file_exists($this->getPackagePath() . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_DASHBOARD . '/install_post.php');
    }

    /**
     * Does this package clear all existing website content when it's being installed?
     *
     * @return bool
     */
    public function allowsFullContentSwap()
    {
        return $this->pkgAllowsFullContentSwap;
    }

    /**
     * Get the absolute path to the package.
     *
     * @return string
     */
    public function getPackagePath()
    {
        $packageHandle = $this->getPackageHandle();
        $result = $this->DIR_PACKAGES . '/' . $packageHandle;
        if (!is_dir($result)) {
            $result = $this->DIR_PACKAGES_CORE . '/' . $packageHandle;
        }

        return $result;
    }

    /**
     * Get the path to the package relative to the web root.
     *
     * @return string
     */
    public function getRelativePath()
    {
        $packageHandle = $this->getPackageHandle();
        if (is_dir($this->DIR_PACKAGES . '/' . $packageHandle)) {
            $result = $this->REL_DIR_PACKAGES . '/' . $packageHandle;
        } else {
            $result = $this->REL_DIR_PACKAGES_CORE . '/' . $packageHandle;
        }

        return $result;
    }

    /**
     * Get the path to the package relative to the Concrete installation folder.
     *
     * @return string
     */
    public function getRelativePathFromInstallFolder()
    {
        return '/' . DIRNAME_PACKAGES . '/' . $this->getPackageHandle();
    }

    /**
     * {@inheritdoc}
     *
     * @see LocalizablePackageInterface::getTranslationFile()
     */
    public function getTranslationFile($locale)
    {
        return $this->getPackagePath() . '/' . DIRNAME_LANGUAGES . "/{$locale}/LC_MESSAGES/messages.mo";
    }

    /**
     * Does this package provide the file thumbnails?
     * If false, the file thumbnails are generated during the install process.
     *
     * @return bool
     */
    public function contentProvidesFileThumbnails()
    {
        return $this->pkgContentProvidesFileThumbnails;
    }

    /**
     * Install the package info row and the database (doctrine entities and db.xml).
     * Packages installing additional content should override this method, call the parent method (`parent::install()`).
     *
     * @param array $data The data received from:
     * - the dashboard/install element of the package when installing via web
     * - the options passed to the CLI command when installing via CLI
     * - <option name="..." value="..." /> elements defined under the <package> element when installing via CIF
     *
     * @return \Concrete\Core\Entity\Package
     */
    public function install(/** array $data */)
    {
        PackageList::refreshCache();
        $em = $this->app->make(EntityManagerInterface::class);
        $package = new PackageEntity();
        $package->setPackageName($this->getPackageName());
        $package->setPackageDescription($this->getPackageDescription());
        $package->setPackageVersion($this->getPackageVersion());
        $package->setPackageHandle($this->getPackageHandle());
        $em->persist($package);
        $em->flush();

        $this->app->make('cache/overrides')->flush();

        $this->installDatabase();

        Localization::clearCache();

        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();

        return $package;
    }

    /**
     * Uninstall the package:
     * - delete the installed items associated to the package
     * - destroy the package proxy classes of entities
     * - remove the package info row.
     */
    public function uninstall()
    {
        /** @var Manager $manager */
        $manager = $this->app->make(Manager::class, ['application' => $this->app]);
        $categories = $manager->getPackageItemCategories();
        $package = $this->getPackageEntity();
        foreach ($categories as $category) {
            if ($category->hasItems($package)) {
                $category->removeItems($package);
            }
        }

        $this->app->make('config')->clearNamespace($this->getPackageHandle());
        $this->app->make('config/database')->clearNamespace($this->getPackageHandle());

        $em = $this->getPackageEntityManager();
        $dbm = null;
        if ($em !== null) {
            $dbm = new DatabaseStructureManager($em);
            $dbm->destroyProxyClasses();
        }

        $em = $this->app->make(EntityManagerInterface::class);
        $em->remove($package);
        $em->flush();

        Localization::clearCache();

        if ($dbm) {
            $dbm->generateProxyClasses();
        }

        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();

    }

    /**
     * Get the contents of the package's CHANGELOG file.
     *
     * @return string if no changelog is available an empty string is returned
     */
    public function getChangelogContents()
    {
        $prefix = $this->getPackagePath() . '/';
        foreach (['CHANGELOG', 'CHANGELOG.txt', 'CHANGELOG.md'] as $name) {
            $file = $prefix . $name;
            if (is_file($file)) {
                $contents = $this->app->make('helper/file')->getContents($file);
                return nl2br(h($contents));
            }
        }

        return '';
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getInstalledList()
     *
     * @return \Concrete\Core\Entity\Package[]
     */
    public static function getInstalledList()
    {
        return app(PackageService::class)->getInstalledList();
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getInstalledHandles()
     *
     * @return string[]
     */
    public static function getInstalledHandles()
    {
        return app(PackageService::class)->getInstalledHandles();
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getByHandle($pkgHandle)
     *
     * @param string $pkgHandle
     *
     * @return \Concrete\Core\Entity\Package|null
     */
    public static function getByHandle($pkgHandle)
    {
        return app(PackageService::class)->getByHandle($pkgHandle);
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getLocalUpgradeablePackages()
     *
     * @return \Concrete\Core\Entity\Package[]
     */
    public static function getLocalUpgradeablePackages()
    {
        return app(PackageService::class)->getLocalUpgradeablePackages();
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getRemotelyUpgradeablePackages()
     *
     * @return \Concrete\Core\Entity\Package[]
     */
    public static function getRemotelyUpgradeablePackages()
    {
        return app(PackageService::class)->getRemotelyUpgradeablePackages();
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getAvailablePackages($filterInstalled)
     *
     * @param bool $filterInstalled
     *
     * @return \Concrete\Core\Package\Package[]
     */
    public static function getAvailablePackages($filterInstalled = true)
    {
        return app(PackageService::class)->getAvailablePackages($filterInstalled);
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getByID($pkgID)
     *
     * @param int $pkgID
     *
     * @return \Concrete\Core\Entity\Package|null
     */
    public static function getByID($pkgID)
    {
        return app(PackageService::class)->getByID($pkgID);
    }

    /**
     * @deprecated
     * Use $app->make('Concrete\Core\Package\PackageService')->getClass($pkgHandle)
     *
     * @param string $pkgHandle
     *
     * @return \Concrete\Core\Package\Package
     */
    public static function getClass($pkgHandle)
    {
        return app(PackageService::class)->getClass($pkgHandle);
    }

    /**
     * Perform tests before this package is installed.
     *
     * @param bool $testForAlreadyInstalled Set to false to skip checking if this package is already installed
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList|true return true if the package can be installed, an ErrorList instance otherwise
     */
    public function testForInstall($testForAlreadyInstalled = true)
    {
        $errors = $this->app->make('error');

        // Step 1 does that package exist ?
        if ($this instanceof BrokenPackage) {
            $errors->add($this->getInstallErrorMessage());
        } elseif ($this->getPackageHandle() === '' || !is_dir($this->getPackagePath())) {
            $errors->add($this->getErrorText(self::E_PACKAGE_NOT_FOUND));
        }

        // Step 2 - check to see if the user has already installed a package w/this handle
        if ($testForAlreadyInstalled) {
            $entity = $this->getPackageEntity();
            if ($entity !== null && $entity->isPackageInstalled()) {
                $errors->add($this->getErrorText(self::E_PACKAGE_INSTALLED));
            }
        }

        if (!$errors->has()) {
            // Step 3 - test minimum application version requirement
            $applicationVersionRequired = $this->getApplicationVersionRequired();
            if (version_compare(APP_VERSION, $applicationVersionRequired, '<')) {
                $errors->add($this->getErrorText([self::E_PACKAGE_VERSION, $applicationVersionRequired]));
            }
            $phpVersionRequired = $this->getPHPVersionIDRequired();
            if ($phpVersionRequired !== null && $phpVersionRequired > PHP_VERSION_ID) {
                $errors->add($this->getErrorText([self::E_PACKAGE_PHP_VERSION, $this->getPHPVersionRequired(), PHP_VERSION]));
            }

            // Step 4 - Check for package dependencies
            $dependencyChecker = $this->app->build(DependencyChecker::class);
            $errors->add($dependencyChecker->testForInstall($this));
        }

        return $errors->has() ? $errors : true;
    }

    /**
     * Perform tests before this package is upgraded.
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList|true return null if the package can be upgraded, an ErrorList instance otherwise
     */
    public function testForUpgrade()
    {
        $result = $this->testForInstall(false);

        return $result;
    }

    /**
     * Perform tests before this package is uninstalled.
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList|true return true if the package can be uninstalled, an ErrorList instance otherwise
     */
    public function testForUninstall()
    {
        $errors = $this->app->make('error');

        /** @var Manager $manager */
        $manager = $this->app->make(Manager::class, ['application' => $this->app]);

        $driver = $manager->driver('theme');
        $themes = $driver->getItems($this->getPackageEntity());

        // Step 1, check for active themes
        $active_theme = Theme::getSiteTheme();
        foreach ($themes as $theme) {
            if ($active_theme->getThemeID() == $theme->getThemeID()) {
                $errors->add($this->getErrorText(self::E_PACKAGE_THEME_ACTIVE));
                break;
            }
        }

        // Step 2, check for package dependencies
        $dependencyChecker = $this->app->build(DependencyChecker::class);
        $errors->add($dependencyChecker->testForUninstall($this));

        return $errors->has() ? $errors : true;
    }

    /**
     * Move the current package directory to the trash directory, and rename it with the package handle and a date code.
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList|static return the Package instance if the package has been moved, an ErrorList instance otherwise
     */
    public function backup()
    {
        $packageHandle = $this->getPackageHandle();
        $errors = $this->app->make('error');
        if ($packageHandle === '' || !is_dir(DIR_PACKAGES . '/' . $packageHandle)) {
            $errors->add($this->getErrorText(self::E_PACKAGE_NOT_FOUND));
        } else {
            $config = $this->app->make('config');
            $trash = $config->get('concrete.misc.package_backup_directory');
            if (!is_dir($trash)) {
                @mkdir($trash, $config->get('concrete.filesystem.permissions.directory'));
            }
            if (!is_dir($trash)) {
                $errors->add($this->getErrorText(self::E_PACKAGE_MIGRATE_BACKUP));
            } else {
                $trashName = $trash . '/' . $packageHandle . '_' . date('YmdHis');
                if (!@rename(DIR_PACKAGES . '/' . $this->getPackageHandle(), $trashName)) {
                    $errors->add($this->getErrorText(self::E_PACKAGE_MIGRATE_BACKUP));
                } else {
                    $this->backedUpFname = $trashName;
                }
            }
        }

        return $errors->has() ? $errors : $this;
    }

    /**
     * If a package was just backed up by this instance of the package object and the packages/package handle directory doesn't exist,
     * this will restore the package from the trash.
     *
     * @return bool
     */
    public function restore()
    {
        $result = false;
        if ($this->backedUpFname !== null && is_dir($this->backedUpFname)) {
            $newPath = DIR_PACKAGES . '/' . $this->getPackageHandle();
            if (!is_dir($newPath)) {
                if (@rename($this->backedUpFname, $newPath)) {
                    $result = true;
                }
            }
        }

        return $result;
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
     * Installs the packages database through doctrine entities and db.xml database definitions.
     */
    public function installDatabase()
    {
        $this->installEntitiesDatabase();
        // Note: this could and should use ContentImporter::IMPORT_MODE_INSTALL instead of IMPORT_MODE_UPGRADE, in order to get better
        // performance, but I'm concerned there are packages out there using `installDatabase` on upgrade routines.
        // If so, making this change would break those. So let's sacrifice performance for backward compatibility
        // here.
        static::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB, ContentImporter::IMPORT_MODE_UPGRADE);
    }

    public function installEntitiesDatabase()
    {
        $em = $this->getPackageEntityManager();
        if ($em !== null) {
            $structure = new DatabaseStructureManager($em);
            $structure->installDatabase();

            // Create or update entity proxies
            $metadata = $em->getMetadataFactory()->getAllMetadata();
            $em->getProxyFactory()->generateProxyClasses($metadata, $em->getConfiguration()->getProxyDir());
        }
    }

    /**
     * Installs a package database from an XML file.
     *
     * @param string $xmlFile Path to the database XML file
     * @param string $importMode - If set to ContentImporter::IMPORT_MODE_UPGRADE, the schema will be checked against
     * the current schema, supporting upgrades at a performance penalty. If set to ContentImporter::IMPORT_MODE_INSTALL
     * the schema will be compared against an empty schema, which is a much faster operation and should be used when
     * you know the installation is taking place against an empty database. ContentImporter::IMPORT_MODE_UPGRADE
     * preserves backward compatibility as this was the way things always used to be handled.
     * @return bool|\stdClass Returns false if the XML file could not be found
     *@throws \Doctrine\DBAL\ConnectionException
     *
     */
    public static function installDB($xmlFile, string $importMode = ContentImporter::IMPORT_MODE_UPGRADE)
    {
        if (!file_exists($xmlFile)) {
            return false;
        }
        if (!in_array($importMode, [ContentImporter::IMPORT_MODE_UPGRADE, ContentImporter::IMPORT_MODE_INSTALL])) {
            throw new \RuntimeException(t('Invalid import mode specified: %s', $importMode));
        }
        $db = app(Connection::class);
        $parser = Schema::getSchemaParser(simplexml_load_file($xmlFile));
        if ($importMode === ContentImporter::IMPORT_MODE_UPGRADE) {
            $parser->setIgnoreExistingTables(false);
        } else {
            // Since we're using an empty schema, we have to use the ignore existing tables option otherwise
            // we might get duped tables like 'btContentLocal' attempting to be installed multiple times because
            // it shows up in db.xml multiple times.
            $parser->setIgnoreExistingTables(true);
        }
        $toSchema = $parser->parse($db);

        if ($importMode === ContentImporter::IMPORT_MODE_UPGRADE) {
            $fromSchema = $db->getSchemaManager()->createSchema();
        } else {
            $fromSchema = new \Doctrine\DBAL\Schema\Schema();
        }
        $comparator = new SchemaComparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $saveQueries = $schemaDiff->toSaveSql($db->getDatabasePlatform());

        if ($saveQueries !== []) {
            $db->beginTransaction();
            foreach ($saveQueries as $query) {
                $db->query($query);
            }
            if ($db->isTransactionActive() && !$db->isAutoCommit()) {
                $db->commit();
            }
        }

        $result = new stdClass();
        $result->result = false;

        return $result;
    }

    /**
     * Updates the package entity name, description and version using the current class properties.
     */
    public function upgradeCoreData()
    {
        $entity = $this->getPackageEntity();
        if ($entity !== null) {
            $em = $this->app->make(EntityManagerInterface::class);
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

        /** @var Manager $manager */
        $manager = $this->app->make(Manager::class, ['application' => $this->app]);
        $items = $manager->driver('block_type')->getItems($this->getPackageEntity());
        foreach ($items as $item) {
            $item->refresh();
        }

        Localization::clearCache();

        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();

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
        if ($em !== null) {
            $this->destroyProxyClasses($em);
            $this->installEntitiesDatabase();
        }
        static::installDB($this->getPackagePath() . '/' . FILENAME_PACKAGE_DB, ContentImporter::IMPORT_MODE_UPGRADE);
    }

    /**
     * Get the namespace of the package by the package handle.
     *
     * @param bool $withLeadingBackslash
     *
     * @return string
     */
    public function getNamespace($withLeadingBackslash = false)
    {
        $leadingBackslash = $withLeadingBackslash ? '\\' : '';

        return $leadingBackslash . 'Concrete\\Package\\' . camelcase($this->getPackageHandle());
    }

    /**
     * Create an entity manager used for the package install, upgrade and unistall process.
     *
     * @return \Doctrine\ORM\EntityManager|null
     */
    public function getPackageEntityManager()
    {
        $providerFactory = new PackageProviderFactory($this->app, $this);
        $provider = $providerFactory->getEntityManagerProvider();
        $drivers = $provider->getDrivers();
        if (empty($drivers)) {
            return null;
        }
        $config = Setup::createConfiguration(true, $this->app->make('config')->get('database.proxy_classes'));
        $driverImpl = new MappingDriverChain();
        $coreDriver = new CoreDriver($this->app);

        // Add all the installed packages so that the new package could potentially extend packages that are already / installed
        $packages = $this->app->make(PackageService::class)->getInstalledList();
        foreach($packages as $package) {
            $existingProviderFactory = new PackageProviderFactory($this->app, $package->getController());
            $existingProvider = $existingProviderFactory->getEntityManagerProvider();
            $existingDrivers = $existingProvider->getDrivers();
            if (!empty($existingDrivers)) {
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
        $db = $this->app->make(Connection::class);

        return EntityManager::create($db, $config);
    }

    /**
     * @deprecated
     * Use $app->make('Doctrine\ORM\EntityManagerInterface')
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->app->make(EntityManagerInterface::class);
    }

    /**
     * @deprecated
     * use the getPackageID method of the package entity
     *
     * @return int|null
     */
    public function getPackageID()
    {
        $packageEntity = $this->getPackageEntity();

        return $packageEntity === null ? null : $packageEntity->getPackageID();
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

    /**
     * Return the package dependencies.
     *
     * @return array
     *
     * @see \Concrete\Core\Package\Package::$packageDependencies
     */
    public function getPackageDependencies()
    {
        return $this->packageDependencies;
    }

    /**
     * Get the error text corresponsing to an error code.
     *
     * @param array|int $errorCode one of the Package::E_PACKAGE_ constants, or an array with the first value is one of the Package::E_PACKAGE_ constants and the other values are used to fill in fields
     *
     * @return string
     */
    protected function getErrorText($errorCode)
    {
        if (is_array($errorCode)) {
            $code = array_shift($errorCode);
            $result = vsprintf($this->getErrorText($code), $errorCode);
        } else {
            $config = $this->app->make('config');
            $dictionary = [
                self::E_PACKAGE_INSTALLED => t("You've already installed that package."),
                self::E_PACKAGE_NOT_FOUND => t('Invalid Package.'),
                self::E_PACKAGE_VERSION => t('This package requires Concrete version %s or greater.'),
                self::E_PACKAGE_DOWNLOAD => t('An error occurred while downloading the package.'),
                self::E_PACKAGE_SAVE => t('Concrete was not able to save the package after download.'),
                self::E_PACKAGE_UNZIP => t('An error occurred while trying to unzip the package.'),
                self::E_PACKAGE_INSTALL => t('An error occurred while trying to install the package.'),
                self::E_PACKAGE_MIGRATE_BACKUP => t('Unable to backup old package directory to %s', $config->get('concrete.misc.package_backup_directory')),
                self::E_PACKAGE_INVALID_APP_VERSION => t('This package isn\'t currently available for this version of Concrete. Please contact the maintainer of this package for assistance.'),
                self::E_PACKAGE_THEME_ACTIVE => t('This package contains the active site theme, please change the theme before uninstalling.'),
                self::E_PACKAGE_PHP_VERSION => t('This package requires PHP version %1$s or greater (the current PHP version is %2$s).'),
            ];
            if (isset($dictionary[$errorCode])) {
                $result = $dictionary[$errorCode];
            } else {
                $result = (string) $errorCode;
            }
        }

        return $result;
    }

    /**
     * Destroys all proxies related to a package.
     */
    protected function destroyProxyClasses(EntityManagerInterface $em)
    {
        $config = $em->getConfiguration();
        $proxyGenerator = new ProxyGenerator($config->getProxyDir(), $config->getProxyNamespace());

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
     * Get the minimum PHP version compatible with the package.
     *
     * @return int|null
     *
     * @example null if the package is compatible with any PHP version that's already compatible with the core
     * @example 80000 if the package requires PHP 8.0.0 and later
     * @example 80100 if the package requires PHP 8.1.0 and later
     * @example 80120 if the package requires PHP 8.1.20 and later
     */
    protected function getPHPVersionIDRequired(): ?int
    {
        $matches = null;
        if (!preg_match('/^(?<major>\d+)(\.(?<minor>\d+)(\.(?<patch>\d+))?)?$/', $this->getPHPVersionRequired(), $matches)) {
            return null;
        }
        $major = (int) $matches['major'];
        $minor = empty($matches['minor']) ? 0 : (int) $matches['minor'];
        $patch = empty($matches['patch']) ? 0 : (int) $matches['patch'];

        return $major * 10000 + $minor * 100 + $patch;
    }
}
