<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Driver\Driver;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * The standard package entity manager provider works in the following ways
 * Class StandardPackageProvider
 * @package Concrete\Core\Database\EntityManager\Provider
 */
class StandardPackageProvider extends AbstractPackageProvider
{

    protected function packageSupportsLegacyCore()
    {
        $concrete5 = '8.0.0a1';
        $package = $this->pkg->getPackageVersion();
        return version_compare($package, $concrete5, '<');
    }

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

        if ($this->packageSupportsLegacyCore()) {
            $reader = $this->getLegacyAnnotationReader();
            if ($this->pkg->enableLegacyNamespace()) {
                // The legacy of the legacies.
                $driver = new Driver(
                    $this->pkg->getNamespace(),
                    new AnnotationDriver($reader, $this->pkg->getPackagePath() . DIRECTORY_SEPARATOR . DIRNAME_CLASSES)
                );
            } else {
                // We have to add Concrete\Package\Whatever mapping to packages/whatever/src/Concrete
            }

            // Now we add in the legacy namespace entity locations for src/PortlandLabs/Entity, etc...
            return array($driver);

        } else {
            $reader = $this->getStandardAnnotationReader();
        }

        return array();


    }
}