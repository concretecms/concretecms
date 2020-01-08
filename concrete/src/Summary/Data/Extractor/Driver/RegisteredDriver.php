<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;


use Concrete\Core\Application\Application;

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
    
    public function inflateClass(Application $app) : DriverInterface
    {
        $driver = $app->make($this->driver);
        return $driver;
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
