<?php
namespace Concrete\Core\Notification\Alert\Filter;

use Concrete\Core\Notification\Type\Manager;
use Concrete\Core\Notification\Type\TypeInterface;

class FilterListFactory
{

    /**
     * @var Manager
     */
    protected $typeManager;

    public function __construct(Manager $typeManager)
    {
        $this->typeManager = $typeManager;
    }

    /**
     * @return FilterList
     */
    public function createList()
    {
        $list = new FilterList();
        foreach($this->typeManager->getDrivers() as $driver) {
            /**
             * @var $driver TypeInterface
             */
            foreach($driver->getAvailableFilters() as $filter) {
                $list->addFilter($filter);
            }
        }

        $list->orderEntries();
        return $list;
    }

}
