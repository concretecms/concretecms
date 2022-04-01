<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
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
     * @param mixed|AbstractValue $value
     * @return mixed
     */
    public function filterByExactValue($list, $value);

}
