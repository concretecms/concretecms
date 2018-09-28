<?php
namespace Concrete\Core\Database\EntityManager\Driver;

use \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

/**
 * Each Doctrine MappingDriver must be wrapped by this class, so all Drivers can be 
 * loaded correctly during the bootstrap of the application
 * Each new Doctrine MappingDriver must also be wrapped in a new instance of this class
 */
class Driver implements DriverInterface
{

    protected $namespace;
    protected $driver;
    
    /**
     * Constructor
     * 
     * @param string $namespace
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver $driver
     */
    public function __construct($namespace, MappingDriver $driver)
    {   
        // Important: Doctrine internally works with namespaces that don't start 
        // with a backslash. If a namespace which starts with a backslash 
        // is provided, doctrine wouldn't find it in the DriverChain and 
        // through a MappingException
        $this->namespace = ltrim($namespace, '\\');
        $this->driver = $driver;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDriver()
    {
        return $this->driver;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
