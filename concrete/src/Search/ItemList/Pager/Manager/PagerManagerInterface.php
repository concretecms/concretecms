<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableInterface;
use Concrete\Core\Search\Pagination\PagerPagination;

interface PagerManagerInterface
{

    /**
     * @param PagerProviderInterface $itemList
     * @param PagerPagination $pagination
     */
    function getNextCursorStart(PagerProviderInterface $itemList, PagerPagination $pagination);

    /**
     * Adds a secondary sort query to an item list so that items that are sorted by
     * text criteria will paginate properly.
     * @param PagerProviderInterface $itemList
     * @param string $direction
     * @return void
     */
    function sortListByCursor(PagerProviderInterface $itemList, $direction);


    /**
     * @return Set
     */
    function getAvailableColumnSet();

    /**
     * @param $cursor
     * @param PagerProviderInterface $itemList
     * @return mixed
     */

    function displaySegmentAtCursor($cursor, PagerProviderInterface $itemList);
}