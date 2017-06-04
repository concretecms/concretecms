<?php
namespace Concrete\Core\Search\Pagination\Adapter;

use Concrete\Core\Search\ItemList\Database\ItemList;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class NextPreviousAdapter implements AdapterInterface
{

    protected $itemList;
    protected $firstResult;
    protected $lastResult;

    public function __construct(ItemList $itemList)
    {
        $this->itemList = $itemList;
    }

    public function getNbResults()
    {
        return -1; // Unknown
    }

    /**
     * @return mixed
     */
    public function getFirstResult()
    {
        return $this->firstResult;
    }

    /**
     * @return mixed
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    public function getSlice($offset, $length)
    {
        $this->itemList
            ->getQueryObject()
            ->setMaxResults($length);
        $results = $this->itemList->getResults();
        $this->firstResult = $results[0];
        $this->lastResult = end($results);
        return $results;
    }


}
