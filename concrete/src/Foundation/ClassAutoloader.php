<?php

declare(strict_types=1);

namespace Concrete\Core\Foundation;

use Concrete\Core\Package\Package;
use Generator;

defined('C5_EXECUTE') or die('Access Denied.');

final class ClassAutoloader
{
    private const FLAG_NONE = 0b0;

    private const FLAG_MODIFIED_PSR4 = 0b1;

    private const MODIFIED_PSR4_SEGMENTS = [
        'Attribute\\' => DIRNAME_ATTRIBUTES . '/',
        'MenuItem\\' => DIRNAME_MENU_ITEMS . '/',
        'Authentication\\' => DIRNAME_AUTHENTICATION . '/',
        'Block\\' => DIRNAME_BLOCKS . '/',
        'Theme\\' => DIRNAME_THEMES . '/',
        'Controller\\PageType\\' => DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/',
        'Controller\\' => DIRNAME_CONTROLLERS . '/',
        'Job\\' => DIRNAME_JOBS . '/',
        'Geolocator\\' => DIRNAME_GEOLOCATION . '/',
    ];

    /**
     * The singleton ClassAutoloader instance.
     *
     * @var \Concrete\Core\Foundation\ClassAutoloader|null
     */
    private static $instance;

    /**
     * Is this instance in the autoload queue?
     *
     * @var bool
     */
    private $hooked = false;

    /**
     * The absolute path to the core directory, always using '/' as directory separator and ending with '/'.
     *
     * @var string
     *
     * @example '/webroot/concrete/'
     * @example 'C:/webroot/concrete/'
     */
    private $coreDir;

    /**
     * The absolute path to the core starting point directory, always using '/' as directory separator and ending with '/'.
     *
     * @var string
     *
     * @example '/webroot/concrete/config/install/packages/'
     * @example 'C:/webroot/concrete/config/install/packages/'
     */
    private $coreStartingPointDir;

    /**
     * The application namespace, with a trailing '\\' and without a leading '\\' (or an empty string if no namespace).
     *
     * @var string
     */
    private $applicationNamespace;

    /**
     * The absolute path to the application directory, always using '/' as directory separator and ending with '/'.
     *
     * @var string
     *
     * @example '/webroot/application/'
     * @example 'C:/webroot/application/'
     */
    private $applicationDir;

    /**
     * The absolute path to the application starting point directory, always using '/' as directory separator and ending with '/'.
     *
     * @var string
     *
     * @example '/webroot/application/config/install/packages/'
     * @example 'C:/webroot/application/config/install/packages/'
     */
    private $applicationStartingPointDir;

    /**
     * Should we enable loading classes in the Application\Src namespace from the /application/src folder?
     *
     * @var bool
     */
    private $applicationLegacyNamespaceEnabled;

    /**
     * The absolute path to the packages directory, always using '/' as directory separator and ending with '/'.
     *
     * @var string
     *
     * @example '/webroot/packages/'
     * @example 'C:/webroot/packages/'
     */
    private $packagesDir;

    /**
     * Array keys are the aliases (without a leading '\'), array values are the actual classes (without a leading '\').
     *
     * @var array
     */
    private $aliases;

    /**
     * List of class aliases that must be autoloaded at boot time (without leading '\').
     *
     * @var string[]
     */
    private $requiredAliases;

    /**
     * List of registered packages.
     * Array keys are the package handles, array values are instances of the package controllers (or NULL if the package controller is not yet available).
     *
     * @var \Concrete\Core\Package\Package[]|null[]
     */
    private $registeredPackages;

    /**
     * Details about the computed autoloading stuff for every package.
     * Array keys are the package handles.
     *
     * @var array[]
     */
    private $packageInfo;

    public function __construct()
    {
        $this->reset(true, true);
    }

    /**
     * Reset this instance values to the default ones.
     *
     * @param bool $aliasesToo should we reset the aliases too?
     * @param bool $packagesToo should we reset the registered packages too?
     *
     * @return $this
     */
    public function reset(bool $aliasesToo = false, $packagesToo = false): self
    {
        if ($aliasesToo) {
            $this->requiredAliases = $this->aliases = [];
        }
        if ($packagesToo) {
            $this->packageInfo = $this->registeredPackages = [];
        }

        return $this
            ->setCoreDir(DIR_BASE_CORE)
            ->setCoreStartingPointDir(DIR_STARTING_POINT_PACKAGES_CORE)
            ->setApplicationNamespace('Application')
            ->setApplicationDir(DIR_APPLICATION)
            ->setApplicationStartingPointDir(DIR_STARTING_POINT_PACKAGES)
            ->setApplicationLegacyNamespaceEnabled(false)
            ->setPackagesDir(DIR_PACKAGES)
        ;
    }

    /**
     * Get the singleton ClassAutoloader instance.
     *
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$instance ?? (self::$instance = new static());
    }

    /**
     * Ensure that this instance is in the autoload queue.
     *
     * @return $this
     */
    public function hook(bool $prepend = false): self
    {
        if (!$this->hooked) {
            spl_autoload_register([$this, 'loadClass'], true, $prepend);
            $this->hooked = true;
        }

        return $this;
    }

    /**
     * Remove this instance from the autoload queue.
     *
     * @return $this
     */
    public function unhook(): self
    {
        if ($this->hooked) {
            spl_autoload_unregister([$this, 'loadClass']);
            $this->hooked = false;
        }

        return $this;
    }

    /**
     * Is this instance in the autoload queue?
     */
    public function isHooked(): bool
    {
        return $this->hooked;
    }

    /**
     * Set the absolute path to the core directory.
     *
     * @return $this
     *
     * @example '/webroot/concrete'
     * @example 'C:\\webroot\\concrete'
     */
    public function setCoreDir(string $value): self
    {
        $this->coreDir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $value), '/') . '/';

        return $this;
    }

    /**
     * Get the absolute path to the core directory, always using '/' as directory separator and ending with '/'.
     *
     * @example '/webroot/concrete/'
     * @example 'C:/webroot/concrete/'
     */
    public function getCoreDir(): string
    {
        return $this->coreDir;
    }

    /**
     * Set the absolute path to the core starting point directory.
     *
     * @return $this
     *
     * @example '/webroot/concrete/config/install/packages'
     * @example 'C:\\webroot\\concrete\\config\\install\\packages'
     */
    public function setCoreStartingPointDir(string $value): self
    {
        $this->coreStartingPointDir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $value), '/') . '/';

        return $this;
    }

    /**
     * Get the absolute path to the core starting point directory, always using '/' as directory separator and ending with '/'.
     *
     * @example '/webroot/concrete/config/install/packages/'
     * @example 'C:/webroot/concrete/config/install/packages/'
     */
    public function getCoreStartingPointDir(): string
    {
        return $this->coreStartingPointDir;
    }

    /**
     * Set the application namespace.
     *
     * @return $this
     *
     * @example 'Application'
     */
    public function setApplicationNamespace(string $value): self
    {
        $value = trim($value, '\\');
        $this->applicationNamespace = $value === '' ? '' : "{$value}\\";

        return $this;
    }

    /**
     * Get the application namespace, with a trailing '\\' and without a leading '\\' (or an empty string if no namespace).
     *
     * @return string
     *
     * @example 'Application\\'
     */
    public function getApplicationNamespace(): string
    {
        return $this->applicationNamespace;
    }

    /**
     * Set the absolute path to the application directory.
     *
     * @return $this
     *
     * @example '/webroot/application'
     * @example 'C:\\webroot\\application'
     */
    public function setApplicationDir(string $value): self
    {
        $this->applicationDir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $value), '/') . '/';

        return $this;
    }

    /**
     * Get the absolute path to the application directory, always using '/' as directory separator and ending with '/'.
     *
     * @example '/webroot/application/'
     * @example 'C:/webroot/application/'
     */
    public function getApplicationDir(): string
    {
        return $this->applicationDir;
    }

    /**
     * Set the absolute path to the application starting point directory.
     *
     * @return $this
     *
     * @example '/webroot/application/config/install/packages'
     * @example 'C:\\webroot\\application\\config\\install\\packages'
     */
    public function setApplicationStartingPointDir(string $value): self
    {
        $this->applicationStartingPointDir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $value), '/') . '/';

        return $this;
    }

    /**
     * Get the absolute path to the application starting point directory, always using '/' as directory separator and ending with '/'.
     *
     * @example '/webroot/application/config/install/packages/'
     * @example 'C:/webroot/application/config/install/packages/'
     */
    public function getApplicationStartingPointDir(): string
    {
        return rtrim($this->applicationStartingPointDir, '/');
    }

    /**
     * Should we enable loading classes in the Application\Src namespace from the /application/src folder?
     *
     * @return $this
     */
    public function setApplicationLegacyNamespaceEnabled(bool $value): self
    {
        $this->applicationLegacyNamespaceEnabled = $value;

        return $this;
    }

    /**
     * Should we enable loading classes in the Application\Src namespace from the /application/src folder?
     */
    public function isApplicationLegacyNamespaceEnabled(): bool
    {
        return $this->applicationLegacyNamespaceEnabled;
    }

    /**
     * Set the absolute path to the packages directory.
     *
     * @return $this
     *
     * @example '/webroot/packages'
     * @example 'C:\\webroot\\packages'
     */
    public function setPackagesDir(string $value): self
    {
        $this->packagesDir = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $value), '/') . '/';

        return $this;
    }

    /**
     * Ghe absolute path to the packages directory, always using '/' as directory separator and ending with '/'.
     *
     * @example '/webroot/packages/'
     * @example 'C:/webroot/packages/'
     */
    public function getPackagesDir(): string
    {
        return $this->packagesDir;
    }

    /**
     * Register a package (given its handle).
     *
     * @return $this
     */
    public function registerPackageHandle(string $packageHandle): self
    {
        if (!isset($this->registeredPackages[$packageHandle])) {
            $this->registeredPackages[$packageHandle] = null;
        }

        return $this;
    }

    /**
     * Register a package (given its controller).
     *
     * @return $this
     */
    public function registerPackageController(Package $packageController): self
    {
        $this->registeredPackages[$packageController->getPackageHandle()] = $packageController;

        return $this;
    }

    /**
     * Unregister a package (given its handle).
     *
     * @return $this
     */
    public function unregisterPackage(string $packageHandle): self
    {
        unset($this->registeredPackages[$packageHandle], $this->packageInfo[$packageHandle]);

        return $this;
    }

    /**
     * Add a class alias.
     *
     * @return $this
     */
    public function addClassAlias(string $alias, string $actual, bool $requiredAtBoot = false): self
    {
        $alias = ltrim($alias, '\\');
        $this->aliases[$alias] = ltrim($actual, '\\');
        if ($requiredAtBoot) {
            $this->requiredAliases[] = $alias;
        }

        return $this;
    }

    /**
     * Add multiple class aliases.
     *
     * @param array $aliass array keys are the aliases, array values are the actual classes
     *
     * @return $this
     */
    public function addClassAliases(array $aliases, bool $requiredAtBoot = false): self
    {
        foreach ($aliases as $alias => $actual) {
            $this->addClassAlias($alias, $actual, $requiredAtBoot);
        }

        return $this;
    }

    /**
     * Get the registered class aliases.
     *
     * @return array array keys are the aliases (without a leading '\'), array values are the actual classes (without a leading '\')
     */
    public function getClassAliases()
    {
        return $this->aliases;
    }

    /**
     * Get the of class aliases that must be autoloaded at boot time (without leading '\').
     *
     * @return string[]
     */
    public function getRequiredAliases()
    {
        return $this->requiredAliases;
    }

    /**
     * Load the class aliases that must be autoloaded at boot time.
     *
     * @return self
     */
    public function autoloadAliasesAtBoot(): self
    {
        foreach ($this->requiredAliases as $alias) {
            class_exists($alias, true);
        }

        return $this;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    public function loadClass(string $class): bool
    {
        return $this->loadClassFromCore($class)
            || $this->loadClassFromPackages($class)
            || $this->loadClassFromApplication($class)
            || $this->loadClassFromAliases($class)
            || $this->loadLegacyClass($class)
        ;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function loadClassFromCore(string $class): bool
    {
        $namespace = NAMESPACE_SEGMENT_VENDOR . '\\';
        if (!str_starts_with($class, $namespace)) {
            return false;
        }
        foreach (self::MODIFIED_PSR4_SEGMENTS as $namespaceSuffix => $directorySuffix) {
            if ($this->loadPSR4Class($class, $namespace . $namespaceSuffix, $this->coreDir . $directorySuffix, self::FLAG_MODIFIED_PSR4)) {
                return true;
            }
        }
        if ($this->loadPSR4Class($class, $namespace . 'StartingPointPackage\\', $this->coreStartingPointDir, self::FLAG_MODIFIED_PSR4)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function loadClassFromPackages(string $class): bool
    {
        foreach ($this->listPackagesInfo($class) as $info) {
            if (isset($info[2])) {
                if ($this->loadPSR4Class($class, $info[0], $info[1], $info[2])) {
                    return true;
                }
            } elseif ($info[0] === $class) {
                if (file_exists($info[1])) {
                    require_once $info[1];

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function loadClassFromApplication(string $class): bool
    {
        if (!str_starts_with($class, $this->applicationNamespace)) {
            return false;
        }
        foreach ([
            'Concrete\\' => $this->applicationDir . DIRNAME_CLASSES . '/Concrete/',
            'Entity\\' => $this->applicationDir . DIRNAME_CLASSES . '/Entity/',
        ] as $namespaceSuffix => $directoryPrefix) {
            if ($this->loadPSR4Class($class, $this->applicationNamespace . $namespaceSuffix, $directoryPrefix, self::FLAG_NONE)) {
                return true;
            }
        }
        foreach (self::MODIFIED_PSR4_SEGMENTS as $namespaceSuffix => $directorySuffix) {
            if ($this->loadPSR4Class($class, $this->applicationNamespace . $namespaceSuffix, $this->applicationDir . $directorySuffix, self::FLAG_MODIFIED_PSR4)) {
                return true;
            }
        }
        foreach ([
            'StartingPointPackage\\' => $this->applicationStartingPointDir,
        ] as $namespaceSuffix => $directoryPrefix) {
            if ($this->loadPSR4Class($class, $this->applicationNamespace . $namespaceSuffix, $directoryPrefix, self::FLAG_MODIFIED_PSR4)) {
                return true;
            }
        }
        if ($this->applicationLegacyNamespaceEnabled) {
            if ($this->loadPSR4Class($class, $this->applicationNamespace . 'Src\\', $this->applicationDir . DIRNAME_CLASSES . '/', self::FLAG_NONE)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function loadClassFromAliases(string $class): bool
    {
        $actualClass = $this->aliases[$class] ?? null;
        if ($actualClass === null) {
            return false;
        }
        // we have an alias for it, but we may not have it yet loaded (because after all, we're in the auto loader.)
        if (!class_exists($actualClass, false)) {
            spl_autoload_call($actualClass);
        }
        class_alias($actualClass, $class);

        return true;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function loadLegacyClass(string $class): bool
    {
        switch (strtolower($class)) {
            case 'loader':
                require_once $this->coreDir . DIRNAME_CLASSES . '/Legacy/Loader.php';

                return true;
            case 'taskpermission':
                require_once $this->coreDir . DIRNAME_CLASSES . '/Legacy/TaskPermission.php';

                return true;
            case 'filepermissions':
                require_once $this->coreDir . DIRNAME_CLASSES . '/Legacy/FilePermissions.php';

                return true;
        }

        return false;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function listPackagesInfo(string $class): Generator
    {
        $packageHandles = array_keys($this->registeredPackages);
        if (str_starts_with($class, NAMESPACE_SEGMENT_VENDOR . '\\')) {
            foreach ($packageHandles as $packageHandle) {
                foreach ($this->listStandardPackageInfo($class, $packageHandle) as $infoList) {
                    foreach ($infoList as $info) {
                        yield $info;
                    }
                }
            }
        }
        foreach ($packageHandles as $packageHandle) {
            foreach ($this->listCustomPackageInfo($packageHandle) as $info) {
                yield $info;
            }
        }
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function listStandardPackageInfo(string $class, string $packageHandle): Generator
    {
        $camelCasePackageHandle = camelcase($packageHandle);
        $packageNamespace = NAMESPACE_SEGMENT_VENDOR . '\\Package\\' . $camelCasePackageHandle . '\\';
        if (!str_starts_with($class, $packageNamespace)) {
            return;
        }
        $packageDirPrefix = $this->packagesDir . $packageHandle . '/';
        if (!isset($this->packageInfo[$packageHandle]['generic'])) {
            $list = [
                [$packageNamespace . 'Controller', $packageDirPrefix . FILENAME_PACKAGE_CONTROLLER],
            ];
            foreach (self::MODIFIED_PSR4_SEGMENTS as $namespaceSuffix => $directorySuffix) {
                $list[] = [
                    $packageNamespace . $namespaceSuffix,
                    $packageDirPrefix . $directorySuffix,
                    self::FLAG_MODIFIED_PSR4,
                ];
            }
            $this->packageInfo[$packageHandle]['generic'] = $list;
        }
        yield $this->packageInfo[$packageHandle]['generic'];
        $packageController = $this->registeredPackages[$packageHandle];
        if ($packageController !== null) {
            if (!isset($this->packageInfo[$packageHandle]['controllerSpecific'])) {
                $list = [];
                if ($packageController->shouldEnableLegacyNamespace()) {
                    $list[] = [
                        $packageNamespace . 'Src\\',
                        $packageDirPrefix . DIRNAME_CLASSES . '/',
                        self::FLAG_NONE,
                    ];
                } else {
                    $list[] = [
                        $packageNamespace,
                        $packageDirPrefix . DIRNAME_CLASSES . '/Concrete/',
                        self::FLAG_NONE,
                    ];
                    $list[] = [
                        $packageNamespace . 'Entity\\',
                        $packageDirPrefix . DIRNAME_CLASSES . '/Entity/',
                        self::FLAG_NONE,
                    ];
                }
                $this->packageInfo[$packageHandle]['controllerSpecific'] = $list;
            }
            yield $this->packageInfo[$packageHandle]['controllerSpecific'];
        }
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     */
    private function listCustomPackageInfo(string $packageHandle): array
    {
        if (isset($this->packageInfo[$packageHandle]['custom'])) {
            return $this->packageInfo[$packageHandle]['custom'];
        }
        $packageController = $this->registeredPackages[$packageHandle];
        if ($packageController === null) {
            return [];
        }
        $registries = $packageController->getPackageAutoloaderRegistries();
        if (isset($this->packageInfo[$packageHandle]['custom'])) {
            // This is not duplicated code (maybe getPackageAutoloaderRegistries already tried class_esists())
            return $this->packageInfo[$packageHandle]['custom'];
        }
        $list = [];
        if (!empty($registries)) {
            $packageDirPrefix = $this->packagesDir . $packageHandle . '/';
            foreach ($registries as $path => $namespacePrefix) {
                $list[] = [
                    trim($namespacePrefix, '\\') . '\\',
                    $packageDirPrefix . trim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/') . '/',
                    self::FLAG_NONE,
                ];
            }
        }
        $this->packageInfo[$packageHandle]['custom'] = $list;

        return $list;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     * @param string $namespacePrefix the namespace prefix (must end with '\')
     * @param string $directoryPrefix the path of the base directory  (must end with '/')
     */
    private function loadPSR4Class(string $class, string $namespacePrefix, string $directoryPrefix, int $flags = self::FLAG_NONE): bool
    {
        if (($file = $this->findPSR4Class($class, $namespacePrefix, $directoryPrefix, $flags)) === '') {
            return false;
        }
        require $file;

        return true;
    }

    /**
     * @param string $class the FQN name of the class (must not start with '\')
     * @param string $namespacePrefix the namespace prefix (must end with '\')
     * @param string $directoryPrefix the path of the base directory  (must end with '/')
     *
     * @return string empty string if not found
     */
    private function findPSR4Class(string $class, string $namespacePrefix, string $directoryPrefix, int $flags): string
    {
        if (!str_starts_with($class, $namespacePrefix)) {
            return '';
        }
        $classWithoutPrefix = substr($class, strlen($namespacePrefix));
        $file = $directoryPrefix . str_replace('\\', '/', $classWithoutPrefix) . '.php';
        if (file_exists($file)) {
            return $file;
        }
        if ($flags & self::FLAG_MODIFIED_PSR4) {
            $chunks = [];
            foreach (explode('\\', $classWithoutPrefix) as $segment) {
                $chunks[] = implode('_', array_map('strtolower', preg_split('/(?=[A-Z])/', lcfirst($segment))));
            }
            $file = $directoryPrefix . implode('/', $chunks) . '.php';
            if (file_exists($file)) {
                return $file;
            }
        }

        return '';
    }
}
