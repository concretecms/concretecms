<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;


class RegisteredDriver 
{

    /**
     * @var string 
     */
    protected $driver = '';

    /**
     * @var int 
     */
    protected $priority = DriverManager::PRIORITY_DEFAULT;

    public function __construct(string $driver, int $priority)
    {
        $this->driver = $driver;
        $this->priority = $priority;
    }
    
    public function inflateClass()
    {
        $driver = new $this->driver();
        return $driver;
    }
    
    public function isValidForObject($object)
    {
        return $this->inflateClass()->isValidForObject($object);
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
    
}
