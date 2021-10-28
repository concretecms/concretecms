<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;


use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Field\DataField;
use Doctrine\Common\Collections\ArrayCollection;

class DriverCollection 
{

    /**
     * @var ArrayCollection 
     */
    protected $drivers;
    
    public function __construct()
    {
        $this->drivers = new ArrayCollection();
    }

    public function addDriver(DriverInterface $driver)
    {
        $this->drivers->add($driver);
    }
    
    public function getDrivers()
    {
        return $this->drivers->toArray();
    }
    
    public function extractData(CategoryMemberInterface $mixed) : Collection
    {
        $collection = new Collection();
        foreach($this->getDrivers() as $driver) {
            $fields = $driver->extractData($mixed);
            foreach($fields->getFields() as $key => $data) {
                $dataField = new DataField($key, $data);
                $collection->addField($dataField);
            }
        }
        return $collection;
    }
    
}
