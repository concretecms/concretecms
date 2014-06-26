<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

class Pagination extends Pagerfanta
{
    /** @var \Concrete\Core\Search\ItemList  */
    protected $list;

    public function __construct(ItemList $itemList, AdapterInterface $adapter) {
        $this->list = $itemList;
        return parent::__construct($adapter);
    }

    public function getTotal()
    {
        return $this->getTotalResults();
    }

    public function getTotalResults()
    {
        return $this->getNbResults();
    }

    public function getTotalPages()
    {
        return $this->getNbPages();
    }

    public function getCurrentPageResults()
    {
        $results = parent::getCurrentPageResults();
        $return = array();
        foreach($results as $result) {
            $r = $this->list->getResult($result);
            if ($r != null) {
                $return[] = $r;
            }
        }
        return $return;
    }
} 