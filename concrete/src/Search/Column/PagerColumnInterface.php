<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

interface PagerColumnInterface
{

    function getColumnKey();
    function getColumnSortDirection();
    function setColumnSortDirection($sort);
    function filterListAtOffset(PagerProviderInterface $itemList, $mixed);

}