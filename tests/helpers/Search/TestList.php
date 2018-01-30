<?php

namespace Concrete\TestHelpers\Search;

use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Tests\Search\PaginationFactoryTest;

class TestList extends ItemList implements PaginationProviderInterface
{
    public function executeGetResults()
    {
        return null;
    }

    public function getResult($mixed)
    {
        return null;
    }

    public function debugStart()
    {
        return null;
    }

    public function debugStop()
    {
        return null;
    }

    public function executeSortBy($field, $direction = 'asc')
    {
        return null;
    }

    public function getTotalResults()
    {
        return null;
    }

    public function getPaginationAdapter()
    {
        return PaginationFactoryTest::getFakeAdapter();
    }
}
