<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Driver\Driver;
use Concrete\Core\Package\Package;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * A simple, standard package provider; provide a Concrete\Core\Package\Package controller, and a path (or array of
 * paths) to PHP entity classes. This uses the annotation driver.
 * Class PackageProvider
 */
class StandardPackageProvider extends AbstractPackageProvider
{
    
    /**
     * @var array
     */
    protected $locations = [];
    
    /**
     * Constructor
     * 
     * @param Application $app
     * @param Package $pkg
     * @param array $locations
     */
    public function __construct(Application $app, Package $pkg, $locations)
    {
        parent::__construct($app, $pkg);
        $this->locations = $locations;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDrivers()
    {
        $drivers = [];
        $reader = $this->getAnnotationReader();
        foreach($this->locations as $path => $prefix) {
            $drivers[] = new Driver(trim($prefix, '\\'),
                new AnnotationDriver($reader, $this->pkg->getPackagePath() . '/' . $path));
        }
        return $drivers;
    }
}
