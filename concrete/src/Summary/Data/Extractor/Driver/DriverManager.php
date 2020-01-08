<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;


use Concrete\Core\Application\Application;

class DriverManager 
{

    
    const PRIORITY_DEFAULT = 1;

    /**
     * @var Application 
     */
    protected $app;
    
    /**
     * @var RegisteredDriver[]
     */
    protected $drivers = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $class
     * @param int $priority
     */
    public function register(string $class, $priority = self::PRIORITY_DEFAULT)
    {
        $this->drivers[] = new RegisteredDriver($class, $priority);    
    }

    /**
     * @return RegisteredDriver[]
     */
    public function getDrivers()
    {
        // Sort the drivers so that the highest score come last, so that the highest scored
        // drivers run and have a chance to override fields set by lower scored drivers.
        usort($this->drivers, function($a, $b) {
            if ($a->getPriority() > $b->getPriority()) {
                return 1;
            } else if ($a->getPriority() < $b->getPriority()) {
                return -1;
            } else {
                return 0;
            }
        });
        return $this->drivers;
    }
    
    /**
     * @param string $category
     * @param $object
     * @return DriverCollection
     */
    public function getDriverCollection($object) : DriverCollection
    {
        $drivers = $this->getDrivers();
        $collection = new DriverCollection();
        foreach($drivers as $driver) {
            $inflatedDriver = $driver->inflateClass($this->app);
            if ($inflatedDriver->isValidForObject($object)) {
                $collection->addDriver($inflatedDriver);
            }
        }
        return $collection;
    }

}
