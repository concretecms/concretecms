<?php

namespace Concrete\Core\Pagination;

use Concrete\Core\Foundation\Collection\ListItemInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class PermissionablePagination extends Pagination
{

    protected $maxResultsToProcessAtOnce = 1000;

    public function __construct(ListItemInterface $itemList)
    {
        $itemList->getQueryObject()->setMaxResults($this->maxResultsToProcessAtOnce);
        $results = $itemList->getResults();
        $adapter = new ArrayAdapter($results);
        return Pagerfanta::__construct($adapter);
    }

    public function getCurrentPageResults()
    {
        return Pagerfanta::getCurrentPageResults();
    }


} 