<?php

namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class PermissionablePagination extends Pagination
{

    protected $maxResultsToProcessAtOnce = 1000;

    public function __construct(ItemList $itemList)
    {
        $itemList->getQueryObject()->setMaxResults($this->maxResultsToProcessAtOnce);
        $results = $itemList->getResults();
        $adapter = new ArrayAdapter($results);
        $this->list = $itemList;
        return Pagerfanta::__construct($adapter);
    }

    public function getCurrentPageResults()
    {
        return Pagerfanta::getCurrentPageResults();
    }


} 