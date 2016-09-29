<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Driver\Driver;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * A simple, standard package provider; provide a Concrete\Core\Package\Package controller, and a path (or array of
 * paths) to PHP entity classes. This uses the annotation driver.
 * Class PackageProvider
 */
class StandardPackageProvider extends AbstractPackageProvider
{

    protected $locations = [];

    public function __construct(Application $app, Package $pkg, $locations)
    {
        parent::__construct($app, $pkg);
        $this->locations = $locations;
    }

    public function getDrivers()
    {
        $drivers = [];
        $reader = $this->getAnnotationReader();
        foreach($this->locations as $path => $prefix) {
            $drivers[] = new Driver(trim($prefix, '\\'),
                new AnnotationDriver($reader, $this->pkg->getPackagePath() . DIRECTORY_SEPARATOR . $path));
        }
        return $drivers;
    }

}