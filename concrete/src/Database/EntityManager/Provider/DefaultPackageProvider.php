<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Driver\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * The standard package entity manager provider. If the package enables legacy namespaces, the provider
 * simply creates a namespace at Concrete\Package\PackageHandle\Src, and considers all classes found at
 * package/package_handle/src potential entities. If the legacy namespace is not enabled, we look for entities
 * at Concrete\Package\PackageHandle\Entity, which maps to packages/package_handle/src/Entity. Additionally, by default
 * any additional autoloader registries are all considered possible entity locations and namespaces.
 *
 * Any package that contains Doctrine entities should consider overriding
 * Concrete\Core\Package\Package::getEntityManagerProvider with a custom method that delivers a tailored
 * class implementing the of the Concrete\Core\Database\EntityManager\Provider\ProviderInterface interface.
 * Class DefaultPackageProvider
 */
class DefaultPackageProvider extends AbstractPackageProvider
{
    /**
     * {@inheritDoc}
     */
    public function getDrivers()
    {

        // The support for a custom Entity path location using the method 
        // 'getPackageEntityPath' was removed in version 8.0.0. Even though 
        // packages using this method still will to work in version 8.0.0, please
        // make sure to migrate your package by using one of the 
        // PackageProvider classes

        // Now, we check to see if no src/ directory exists. If none exists, we return no entity manager
        if (!is_dir($this->pkg->getPackagePath() . '/' . DIRNAME_CLASSES)) {
            return array();
        }

        $reader = $this->getAnnotationReader();

        if ($this->pkg->shouldEnableLegacyNamespace()) {
            // The legacy of the legacies.
            $path = $this->pkg->getPackagePath() . '/' . DIRNAME_CLASSES;
            if (is_dir($path)) {
                $driver = new Driver(
                    $this->pkg->getNamespace() . '\Src',
                    new AnnotationDriver($reader, $path)
                );
            }
        } else {
            // We have to add Concrete\Package\Whatever mapping to packages/whatever/src/Concrete
            $path = $this->pkg->getPackagePath() . '/' . DIRNAME_CLASSES . '/Concrete/' . DIRNAME_ENTITIES;
            if (is_dir($path)) {
                $driver = new Driver(
                    $this->pkg->getNamespace() . '\Entity',
                    new AnnotationDriver($reader, $path)
                );
            }
        }

        $drivers = [];
        if (isset($driver)) {
            $drivers[] = $driver;
        }

        // Now if there are any autoloader entries, we automatically make those entity locations as well.
        foreach($this->pkg->getPackageAutoloaderRegistries() as $path => $prefix) {
            $drivers[] = new Driver(trim($prefix, '\\'),
                new AnnotationDriver($reader, $this->pkg->getPackagePath() . '/' . $path)
            );
        }

        return $drivers;
    }
}
