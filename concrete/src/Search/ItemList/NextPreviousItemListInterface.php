<?php
namespace Concrete\Core\Search\ItemList;


use Concrete\Core\Search\Pagination\NextPreviousPagination;
use Doctrine\DBAL\Query\QueryBuilder;

interface NextPreviousItemListInterface
{

    function getQueryOffsetNextPageValues(NextPreviousPagination $pagination);
    function getQueryOffsetNextPageParameter($column = null);
    function filterQueryByOffset(QueryBuilder $query, $requestData);

}