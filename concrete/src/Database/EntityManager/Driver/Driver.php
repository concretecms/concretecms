<?php
namespace Concrete\Core\Database\EntityManager\Driver;

use Concrete\Core\Application\Application;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class Driver implements DriverInterface
{

    protected $namespace;
    protected $driver;

    public function __construct($namespace, $driver)
    {
        $this->namespace = $namespace;
        $this->driver = $driver;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }


}
