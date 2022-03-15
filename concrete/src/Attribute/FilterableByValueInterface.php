<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Search\ItemList\ItemList;

/**
 * Attribute controllers should implement this interface if they want to make it possible to filter
 * a result list by the exact value of an attribute. This enables support things like unique/SKU
 * filtering in Express.
 */
interface FilterableByValueInterface
{

    /**
     * Filters list by exact value
     *
     * @var ItemList $list
     * @param Value $value
     * @return mixed
     */
    public function filterByExactValue($list, Value $value);

}
