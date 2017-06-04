<?php
namespace Concrete\Core\Search\ItemList;


use Concrete\Core\Search\Pagination\NextPreviousPagination;

interface NextPreviousItemListInterface
{

    function getQueryOffsetNextPageValues(NextPreviousPagination $pagination);
    function getQueryOffsetNextPageParameter();

}