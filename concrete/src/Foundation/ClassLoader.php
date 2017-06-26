<?php
namespace Concrete\Core\Foundation;

use Concrete\Core\Package\Package;
use Concrete\Core\Foundation\Psr4ClassLoader;

/**
 * Provides autoloading for concrete5
 * Typically getInstance() should be used rather than instantiating a new object.
 *
 * \@package Concrete\Core\Foundation
 */
class ClassLoader
{
    /** @var ClassLoader */
    public static $instance;

    /**
     * @var ClassLoaderInterface[]
     */
    protected $loaders;

    protected $enableLegacyNamespace = false;

    protected $applicationNamespace = 'Application';

    /**
     * Returns the status of the legacy namespace
     * @return boolean
     */
    public function legacyNamespaceEnabled()
    {
        return $this->enableLegacyNamespace;
    }

    /**
     * Set legacy namespaces to enabled. This method unsets and resets this loader.
     */
    public function enableLegacyNamespace()
    {
        $this->enableLegacyNamespace = true;
        $this->disable();
        $this->activateAutoloaders();
        $this->enable();
    }

    /**
     * Set legacy namespaces to disabled. This method unsets and resets this loader.
     */
    public function disableLegacyNamespace()
    {
        $this->enableLegacyNamespace = false;
        $this->disable();
        $this->activateAutoloaders();
        $this->enable();
    }

    protected function activateAutoloaders()
    {
        $this->loaders = array();
        $this->setupLegacyAutoloading();
        $this->setupCoreAutoloading(); // Modified PSR4
        $this->setupCoreSourceAutoloading(); // Strict PSR4
    }

    public function reset()
    {
        $this->disable();
        $this->loaders = array();
        $this->enableLegacyNamespace = false;
        $this->applicationNamespace = 'Application';
        $this->setupLegacyAutoloading();
        $this->setupCoreAutoloading(); // Modified PSR4
        $this->setupCoreSourceAutoloading(); // Strict PSR4
        $this->enable();
    }


    /**
     * @return string
     */
    public function getApplicationNamespace()
    {
        return $this->applicationNamespace;
    }

    /**
     * @param string $applicationNamespace
     */
    public function setApplicationNamespace($applicationNamespace)
    {
        $this->applicationNamespace = $applicationNamespace;
        $this->disable();
        $this->activateAutoloaders();
        $this->enable();
    }


    public function __construct($enableLegacyNamespace = false, $applicationNamespace = 'Application')
    {
        $this->enableLegacyNamespace = $enableLegacyNamespace;
        $this->applicationNamespace = $applicationNamespace;
        $this->activateAutoloaders();
        $this->enableAliasClassAutoloading();
    }

    /**
     * Aliases concrete5 classes to shorter class name aliases.
     *
     * IDEs will not recognize these classes by default. A symbols file can be generated to
     * assist IDEs by running SymbolGenerator::render() via PHP or executing the command-line
     * 'concrete/bin/concrete5 c5:ide-symbols
     */
    protected function enableAliasClassAutoloading()
    {
        $list = ClassAliasList::getInstance();
        $loader = new AliasClassLoader($list);
        $loader->register(); // We can't add this to the loaders array because there's no way to unregister these once they're registered
    }

    protected function setupLegacyAutoloading()
    {
        $mapping = array(
            'Loader' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/Loader.php',
            'TaskPermission' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/TaskPermission.php',
            'FilePermissions' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/FilePermissions.php',
        );

        $loader = new MapClassLoader($mapping);
        $this->loaders[] = $loader;
    }

    protected function setupCoreAutoloading()
    {
        $loader = new ModifiedPSR4ClassLoader();
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\StartingPointPackage',
            DIR_BASE_CORE . '/config/install/' . DIRNAME_PACKAGES);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Attribute', DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\MenuItem', DIR_BASE_CORE . '/' . DIRNAME_MENU_ITEMS);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Authentication',
            DIR_BASE_CORE . '/' . DIRNAME_AUTHENTICATION);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Block', DIR_BASE_CORE . '/' . DIRNAME_BLOCKS);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Theme', DIR_BASE_CORE . '/' . DIRNAME_THEMES);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Controller\\PageType',
            DIR_BASE_CORE . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Controller', DIR_BASE_CORE . '/' . DIRNAME_CONTROLLERS);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Job', DIR_BASE_CORE . '/' . DIRNAME_JOBS);

        $loader->addPrefix($this->getApplicationNamespace() . '\\StartingPointPackage',
            DIR_APPLICATION . '/config/install/' . DIRNAME_PACKAGES);
        $loader->addPrefix($this->getApplicationNamespace() . '\\Attribute',
            DIR_APPLICATION . '/' . DIRNAME_ATTRIBUTES);
        $loader->addPrefix($this->getApplicationNamespace() . '\\MenuItem', DIR_APPLICATION . '/' . DIRNAME_MENU_ITEMS);
        $loader->addPrefix($this->getApplicationNamespace() . '\\Authentication',
            DIR_APPLICATION . '/' . DIRNAME_AUTHENTICATION);
        $loader->addPrefix($this->getApplicationNamespace() . '\\Block', DIR_APPLICATION . '/' . DIRNAME_BLOCKS);
        $loader->addPrefix($this->getApplicationNamespace() . '\\Theme', DIR_APPLICATION . '/' . DIRNAME_THEMES);
        $loader->addPrefix($this->getApplicationNamespace() . '\\Controller\\PageType',
            DIR_APPLICATION . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES);
        $loader->addPrefix($this->getApplicationNamespace() . '\\Controller',
            DIR_APPLICATION . '/' . DIRNAME_CONTROLLERS);
        $loader->addPrefix($this->getApplicationNamespace() . '\\Job', DIR_APPLICATION . '/' . DIRNAME_JOBS);
        $this->loaders[] = $loader;
    }

    public function setupCoreSourceAutoloading()
    {
        $loader = new Psr4ClassLoader();

        // Handle class core extensions like antispam and captcha with Application\Concrete\MyCaptchaLibrary
        $loader->addPrefix($this->getApplicationNamespace() . '\\Concrete',
            DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/Concrete');

        // Application entities
        $loader->addPrefix($this->getApplicationNamespace() . '\\Entity',
            DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/Entity');

        if ($this->legacyNamespaceEnabled()) {
            $loader->addPrefix($this->getApplicationNamespace() . '\\Src', DIR_APPLICATION . '/' . DIRNAME_CLASSES);
        }

        $this->loaders[] = $loader;
    }

    public function registerPackage($pkg)
    {
        if (is_string($pkg)) {
            $pkg = \Package::getClass($pkg);
        }

        $pkgHandle = $pkg->getPackageHandle();

        $loader = new ModifiedPSR4ClassLoader();
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Attribute',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_ATTRIBUTES);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\MenuItem',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MENU_ITEMS);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Authentication',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_AUTHENTICATION);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Block',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_BLOCKS);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Theme',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_THEMES);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Controller\\PageType',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Controller',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CONTROLLERS);
        $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Job',
            DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JOBS);

        $this->loaders[] = $loader;
        $loader->register();

        $loader = new Psr4ClassLoader();

        /** @type Package $pkg */
        if (!$pkg->shouldEnableLegacyNamespace()) {
            // We map all src files in the package to the src/Concrete directory
            $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle),
                DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CLASSES . '/Concrete');

            $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Entity',
                DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CLASSES . '/Entity');

        } else {
            // legacy Src support
            $loader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Src',
                DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CLASSES);
        }

        $this->loaders[] = $loader;
        $loader->register();

        $this->registerPackageController($pkgHandle);
        $this->registerPackageCustomAutoloaders($pkg);
    }

    public function registerPackageController($pkgHandle)
    {
        $loader = new MapClassLoader(array(
            NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Controller' => DIR_PACKAGES . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER,
        ));
        $this->loaders[] = $loader;
        $loader->register();
    }

    public function registerPackageCustomAutoloaders($pkg)
    {
        if (is_string($pkg)) {
            $pkg = \Package::getClass($pkg);
        }

        $pkgHandle = $pkg->getPackageHandle();

        $loader = new Psr4ClassLoader();
        $loaders = $pkg->getPackageAutoloaderRegistries();
        if ($loaders && count($loaders) > 0) {
            foreach ($loaders as $path => $prefix) {
                $loader->addPrefix($prefix, DIR_PACKAGES . '/' . $pkgHandle . '/' . $path);
            }
        }

        $this->loaders[] = $loader;
        $loader->register();
    }


    /**
     * Returns the ClassLoader instance.
     *
     * @return ClassLoader
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function enable()
    {
        foreach ($this->loaders as $loader) {
            $loader->register();
        }
    }

    public function disable()
    {
        foreach ($this->loaders as $loader) {
            $loader->unregister();
        }
    }

}
