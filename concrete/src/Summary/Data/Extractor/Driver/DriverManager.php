<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;


class DriverManager 
{

    const PRIORITY_DEFAULT = 1;
    
    protected $drivers = [];

    /**
     * @param string $class
     * @param int $priority
     */
    public function register(string $class, $priority = self::PRIORITY_DEFAULT)
    {
        $this->drivers[] = new RegisteredDriver($class, $priority);    
    }
    
    public function getDrivers()
    {
        usort($this->drivers, function($a, $b) {
            if ($a->getPriority() > $b->getPriority()) {
                return -1;
            } else if ($a->getPriority() < $b->getPriority()) {
                return 1;
            } else {
                return 0;
            }
        });
        return $this->drivers;
    }
    
    /**
     * @param string $category
     * @param $object
     * @return DriverInterface
     */
    public function getDriver($object) : ?DriverInterface
    {
        $drivers = $this->getDrivers();
        foreach($drivers as $driver) {
            if ($driver->isValidForObject($object)) {
                return $driver->inflateClass();
            }
        }
    }

}
