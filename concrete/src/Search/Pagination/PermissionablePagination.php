<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Facade;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Processes requests and builds pagination out of them.
 */
class PermissionablePagination extends Pagination
{
    protected $maxResultsToProcessAtOnce;

    public function __construct(ItemList $itemList)
    {
        $app = Facade::getFacadeApplication();
        $this->maxResultsToProcessAtOnce = $app['config']->get('concrete.limits.permissionable_pagination_max_results');
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
