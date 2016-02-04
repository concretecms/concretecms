<?php
namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Liaison;

abstract class Package implements LocalizablePackageInterface
{

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

    public function getPackagePath()
    {
        $dirp = (is_dir(
            DIR_PACKAGES . '/' . $this->getPackageHandle())) ? DIR_PACKAGES : DIR_PACKAGES_CORE;
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
            $this->DIR_PACKAGES . '/' . $this->getPackageHandle())) ? REL_DIR_PACKAGES : REL_DIR_PACKAGES_CORE;

        return $dirp . '/' . $this->pkgHandle;
    }


    public function getTranslationFile($locale)
    {
        $path = $this->getPackagePath() . '/' . DIRNAME_LANGUAGES;
        $languageFile = "$path/$locale/LC_MESSAGES/messages.mo";
        return $languageFile;
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

}
