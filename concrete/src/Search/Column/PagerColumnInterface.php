<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

/**
 * @since 8.2.0
 */
interface PagerColumnInterface
{

    function getColumnKey();
    function getColumnSortDirection();
    function setColumnSortDirection($sort);
    function filterListAtOffset(PagerProviderInterface $itemList, $mixed);

}