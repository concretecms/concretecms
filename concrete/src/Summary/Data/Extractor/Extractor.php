<?php
namespace Concrete\Core\Summary\Data\Extractor;

use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;

/**
 * Responsible for taking an object like Page, and extracting normalized data. 
 */
class Extractor 
{

    /**
     * @var DriverManager 
     */
    protected $driverManager;
    
    public function __construct(DriverManager $driverManager)
    {
        $this->driverManager = $driverManager;
    }

    /**
     * Extracts data from a mixed $object. Takes a category handle
     * which is used to create the extractor library.
     * 
     * @param string $category
     * @param $object
     */
    public function extract(string $category, $object)
    {
        $driver = $this->driverManager->getDriver($category, $object);
    }

}
