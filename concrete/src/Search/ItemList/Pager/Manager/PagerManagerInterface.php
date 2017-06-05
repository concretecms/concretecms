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
     * @return VariableInterface[]
     */
    function getNextPageVariables(PagerProviderInterface $itemList, PagerPagination $pagination);

    function filterByVariable(VariableInterface $variable, PagerProviderInterface $itemList);
}