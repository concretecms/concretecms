<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;

interface DriverInterface 
{

    /**
     * @return string
     */
    public function getCategory();
    
    /**
     * Returns true if the driver can/should be used with this object.
     * 
     * @param $mixed
     * @return bool
     */
    public function isValidForObject($mixed) : bool;

    /**
     * Extracts data from an object and returns it normalized into a collection.
     * 
     * @param CategoryMemberInterface $mixed
     * @return Collection
     */
    public function extractData(CategoryMemberInterface $mixed) : Collection;
    
}
