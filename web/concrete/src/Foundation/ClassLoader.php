<?php

namespace Concrete\Core\Foundation;

use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Package\Package;
use \Concrete\Core\Foundation\ModifiedPSR4ClassLoader;
use \Symfony\Component\ClassLoader\MapClassLoader as SymfonyMapClassloader;
use Symfony\Component\ClassLoader\Psr4ClassLoader as SymfonyClassLoader;

/**
 * Provides autoloading for concrete5
 * Typically getInstance() should be used rather than instantiating a new object.
 * @package Concrete\Core\Foundation
 */
class ClassLoader
{

    /** @var ClassLoader */
    static $instance;
    protected $classAliases = array();

    /**
     * Returns the ClassLoader instance
     * @return ClassLoader
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new Classloader();
        }
        return static::$instance;
    }

    public function __construct()
    {
        $this->setupFileAutoloader();
        $this->setupAliasAutoloader();
        $this->setupMapClassAutoloader();
    }

    /**
     * Maps legacy classes
     */
    protected function setupMapClassAutoloader()
    {
        $mapping = array(
            'Loader' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/Loader.php',
            'TaskPermission' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/TaskPermission.php',
            'FilePermissions' => DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Legacy/FilePermissions.php'
        );

        $loader = new SymfonyMapClassloader($mapping);
        $loader->register();
    }

    /**
     * Aliases concrete5 classes to shorter class name aliases
     *
     * IDEs will not recognize these classes by default. A symbols file can be generated to
     * assist IDEs by running SymbolGenerator::render() via PHP or executing the command-line
     * 'concrete/bin/concrete5 c5:ide-symbols
     */
    protected function setupAliasAutoloader()
    {
        $loader = $this;
        spl_autoload_register(function ($class) use ($loader) {
            $list = ClassAliasList::getInstance();
            if (array_key_exists($class, $aliases = $list->getRegisteredAliases())) {
                // we have an alias for it, but we don't have it yet loaded
                // (because after all, we're in the auto loader.)
                $fullClass = $aliases[$class];
                if (!class_exists($fullClass, false)) {
                    spl_autoload_call($fullClass);
                }
                // finally, we set up a class alias for this list. We do this now because
                // we don't know earlier what namespace it'll be in
                class_alias($fullClass, $class);
            }
        });
    }

    /**
     * Registers the prefixes for a package
     *
     * The following prefixes are registered:
     * <ul>
     * <li>`Concrete\Package\PkgHandle\Attribute` -> `packages/pkg_handle/attributes`</li>
     * <li>`Concrete\Package\PkgHandle\MenuItem` -> `packages/pkg_handle/menu_items`</li>
     * <li>`Concrete\Package\PkgHandle\Authentication` -> `packages/pkg_handle/authentication`</li>
     * <li>`Concrete\Package\PkgHandle\Block` -> `packages/pkg_handle/blocks`</li>
     * <li>`Concrete\Package\PkgHandle\Theme` -> `packages/pkg_handle/themes`</li>
     * <li>`Concrete\Package\PkgHandle\Controller\PageType` -> `packages/pkg_handle/controllers/page_type`</li>
     * <li>`Concrete\Package\PkgHandle\Controller` -> `packages/pkg_handle/controllers`</li>
     * <li>`Concrete\Package\PkgHandle\Job` -> `packages/pkg_handle/jobs`</li>
     * </ul>
     *
     * If Package::$pkgAutoloaderMapCoreExtensions is true, all remaining class paths will be checked for
     * under packages/pkg_handle/src/Concrete
     *
     * Otherwise, `Concrete\Package\PkgHandle\Src` -> `packages/pkg_handle/src` will be registered
     *
     * The function Package::getPackageAutoloaderRegistries() can be used to add custom prefixes
     *
     * @param string|\Package $pkg Package handle or an instance of the package controller
     * @see Package::$pkgAutoloaderMapCoreExtensions, Package::getPackageAutoloaderRegistries()
     */
    public function registerPackage($pkg)
    {
        if (!($pkg instanceof Package)) {
            $pkg = \Package::getClass($pkg);
        }

        $pkgHandle = $pkg->getPackageHandle();
        $symfonyLoader = new ModifiedPSR4ClassLoader();
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Attribute', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_ATTRIBUTES);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\MenuItem', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MENU_ITEMS);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Authentication', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_AUTHENTICATION);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Block', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_BLOCKS);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Theme', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_THEMES);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Controller\\PageType', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Controller', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CONTROLLERS);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Job', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JOBS);

        $strictLoader = new SymfonyClassLoader();
        $loaders = $pkg->getPackageAutoloaderRegistries();
        if (count($loaders) > 0) {
            foreach ($loaders as $path => $prefix) {
                $strictLoader->addPrefix($prefix, DIR_PACKAGES . '/' . $pkgHandle . '/' . $path);
            }
        }

        if ($pkg->providesCoreExtensionAutoloaderMapping()) {
            // We map all src files in the package to the src/Concrete directory
            $strictLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle), DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CLASSES . '/Concrete');
        } else {
            // legacy Src support
            $strictLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Src', DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CLASSES);
        }

        $symfonyLoader->register();
        $strictLoader->register();
        $this->registerPackageController($pkgHandle);

    }

    /**
     * Maps a package controller's class name to the file
     * @param string $pkgHandle Handle of package
     */
    public function registerPackageController($pkgHandle)
    {
        $symfonyLoader = new SymfonyMapClassloader(array(
            NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . camelcase($pkgHandle) . '\\Controller' =>
                DIR_PACKAGES . '/' . $pkgHandle . '/' . FILENAME_PACKAGE_CONTROLLER
        ));
        $symfonyLoader->register();

    }

    /**
     * Adds concrete5's core autoloading prefixes
     *
     * * The following prefixes are registered:
     * <ul>
     * <li>`Concrete\StartingPointPackage` -> `concrete/config/install/packages`</li>
     * <li>`Concrete\Attribute` -> `concrete/attributes`</li>
     * <li>`Concrete\Authentication` -> `concrete/authentication`</li>
     * <li>`Concrete\Block` -> `concrete/blocks`</li>
     * <li>`Concrete\Theme` -> `concrete/themes`</li>
     * <li>`Concrete\Controller\PageType` -> `concrete/controllers/page_types`</li>
     * <li>`Concrete\Controller` -> `concrete/controllers`</li>
     * <li>`Concrete\Job` -> `concrete/jobs`</li>
     * <li>`Concrete\Core` -> `concrete/src`</li>
     * <li>`Application\StartingPointPackage` -> `application/config/install/packages`</li>
     * <li>`Application\Attribute` -> `application/attributes`</li>
     * <li>`Application\Authentication` -> `application/authentication`</li>
     * <li>`Application\Block` -> `application/blocks`</li>
     * <li>`Application\Theme` -> `application/themes`</li>
     * <li>`Application\Controller\PageType` -> `application/controllers/page_types`</li>
     * <li>`Application\Controller` -> `application/controllers`</li>
     * <li>`Application\Job` -> `application/jobs`</li>
     * <li>`Application\Core` -> `application/src`</li>
     * </ul>
     *
     * The application namespace can be customized by setting `namespace` in the application's `config/app.php`.
     */
    protected function setupFileAutoloader()
    {
        $symfonyLoader = new ModifiedPSR4ClassLoader();

        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\StartingPointPackage', DIR_BASE_CORE . '/config/install/' . DIRNAME_PACKAGES);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Attribute', DIR_BASE_CORE . '/' . DIRNAME_ATTRIBUTES);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\MenuItem', DIR_BASE_CORE . '/' . DIRNAME_MENU_ITEMS);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Authentication', DIR_BASE_CORE . '/' . DIRNAME_AUTHENTICATION);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Block', DIR_BASE_CORE . '/' . DIRNAME_BLOCKS);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Theme', DIR_BASE_CORE . '/' . DIRNAME_THEMES);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Controller\\PageType', DIR_BASE_CORE . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Controller', DIR_BASE_CORE . '/' . DIRNAME_CONTROLLERS);
        $symfonyLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Job', DIR_BASE_CORE . '/' . DIRNAME_JOBS);


        $namespace = 'Application';
        $app_config_path = DIR_APPLICATION . '/config/app.php';
        $provideCoreExtensionAutoloaderMapping = false;
        if (file_exists($app_config_path)) {
            $app_config = require $app_config_path;
            if (isset($app_config['namespace'])) {
                $namespace = $app_config['namespace'];
            }
            if (isset($app_config['provide_core_extension_autoloader_mapping'])) {
                $provideCoreExtensionAutoloaderMapping = true;
            }
        }
        $symfonyLoader->addPrefix($namespace . '\\StartingPointPackage', DIR_APPLICATION . '/config/install/' . DIRNAME_PACKAGES);
        $symfonyLoader->addPrefix($namespace . '\\Attribute', DIR_APPLICATION . '/' . DIRNAME_ATTRIBUTES);
        $symfonyLoader->addPrefix($namespace . '\\MenuItem', DIR_APPLICATION . '/' . DIRNAME_MENU_ITEMS);
        $symfonyLoader->addPrefix($namespace . '\\Authentication', DIR_APPLICATION . '/' . DIRNAME_AUTHENTICATION);
        $symfonyLoader->addPrefix($namespace . '\\Block', DIR_APPLICATION . '/' . DIRNAME_BLOCKS);
        $symfonyLoader->addPrefix($namespace . '\\Theme', DIR_APPLICATION . '/' . DIRNAME_THEMES);
        $symfonyLoader->addPrefix($namespace . '\\Controller\\PageType', DIR_APPLICATION . '/' . DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES);
        $symfonyLoader->addPrefix($namespace . '\\Controller', DIR_APPLICATION . '/' . DIRNAME_CONTROLLERS);
        $symfonyLoader->addPrefix($namespace . '\\Job', DIR_APPLICATION . '/' . DIRNAME_JOBS);

        $symfonyLoader->register();

        $strictLoader = new SymfonyClassLoader();

        $strictLoader->addPrefix(NAMESPACE_SEGMENT_VENDOR . '\\Core', DIR_BASE_CORE . '/' . DIRNAME_CLASSES);
        if ($provideCoreExtensionAutoloaderMapping) {
            $strictLoader->addPrefix($namespace, DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/Concrete');
        } else {
            $strictLoader->addPrefix($namespace . '\\Src', DIR_APPLICATION . '/' . DIRNAME_CLASSES);
        }
        $strictLoader->register();
    }


}
