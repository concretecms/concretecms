<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Driver\Driver;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * The standard package entity manager provider works in the following ways
 * Class DefaultPackageProvider
 * @package Concrete\Core\Database\EntityManager\Provider
 */
class DefaultPackageProvider extends AbstractPackageProvider
{

    public function getDrivers()
    {
        // First, we check to see if this package has a custom deprecated getPackageEntityPath method. If so
        // We return a single annotation driver using legacy annotations, pointing to that entity path.
        if (method_exists($this->pkg, 'getPackageEntityPath')) {
            return new AnnotationDriver($this->getLegacyAnnotationReader(), $this->pkg->getPackageEntityPath());
        }

        // Now, we check to see if no src/ directory exists. If none exists, we return no entity manager
        if (!is_dir($this->pkg->getPackagePath() . DIRECTORY_SEPARATOR . DIRNAME_CLASSES)) {
            return array();
        }

        $reader = $this->getAnnotationReader();

        if ($this->pkg->enableLegacyNamespace()) {
            // The legacy of the legacies.
            $path = $this->pkg->getPackagePath() . DIRECTORY_SEPARATOR . DIRNAME_CLASSES;
            if (is_dir($path)) {
                $driver = new Driver(
                    $this->pkg->getNamespace() . '\Src',
                    new AnnotationDriver($reader, $path)
                );
            }
        } else {
            // We have to add Concrete\Package\Whatever mapping to packages/whatever/src/Concrete
            $path = $this->pkg->getPackagePath() . DIRECTORY_SEPARATOR . DIRNAME_CLASSES . '/Concrete/' . DIRNAME_ENTITIES;
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
                new AnnotationDriver($reader, $this->pkg->getPackagePath() . DIRECTORY_SEPARATOR . $path)
            );
        }

        return $drivers;
    }
}