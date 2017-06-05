<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

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

    function displaySegmentAtCursor($cursor, PagerProviderInterface $itemList);
}