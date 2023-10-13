<?php
namespace Concrete\Core\Foundation;

/**
 * @deprecated
 *
 * @see \Concrete\Core\Foundation\ClassAutoloader
 */
class ClassLoader
{
    public static $instance;

    public function legacyNamespaceEnabled()
    {
        return ClassAutoloader::getInstance()->isApplicationLegacyNamespaceEnabled();
    }

    /**
     * Set legacy namespaces to enabled. This method unsets and resets this loader.
     */
    public function enableLegacyNamespace()
    {
        ClassAutoloader::getInstance()->setApplicationLegacyNamespaceEnabled(true);
    }

    public function disableLegacyNamespace()
    {
        ClassAutoloader::getInstance()->setApplicationLegacyNamespaceEnabled(false);
    }

    protected function activateAutoloaders()
    {
    }

    public function reset()
    {
        ClassAutoloader::getInstance()->reset();
    }


    /**
     * @return string
     */
    public function getApplicationNamespace()
    {
        return rtrim(ClassAutoloader::getInstance()->getApplicationNamespace(), '\\');
    }

    /**
     * @param string $applicationNamespace
     */
    public function setApplicationNamespace($applicationNamespace)
    {
        ClassAutoloader::getInstance()->setApplicationNamespace((string) $applicationNamespace);
    }


    public function __construct($enableLegacyNamespace = false, $applicationNamespace = 'Application')
    {
    }

    protected function enableAliasClassAutoloading()
    {
    }

    protected function setupLegacyAutoloading()
    {
    }

    protected function setupCoreAutoloading()
    {
    }

    public function setupCoreSourceAutoloading()
    {
    }

    public function registerPackage($pkg)
    {
        $autoloader = ClassAutoloader::getInstance();
        if (is_string($pkg)) {
            $autoloader->registerPackageHandle($pkg);
        } elseif ($pkg instanceof \Concrete\Core\Entity\Package) {
            $autoloader->registerPackageController($pkg->getController());
        } else {
            $autoloader->registerPackageController($pkg);
        }
    }

    public function registerPackageController($pkgHandle)
    {
        $this->registerPackage($pkgHandle);
    }

    public function registerPackageCustomAutoloaders($pkg)
    {
        $this->registerPackage($pkg);
    }

    public static function getInstance()
    {
        return static::$instance ?? (static::$instance = new self());
    }

    public function enable()
    {
        ClassAutoloader::getInstance()->hook();
    }

    public function disable()
    {
        ClassAutoloader::getInstance()->unhook();
    }

}
