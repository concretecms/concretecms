<?php
namespace Concrete\Core\Pagination;

use Concrete\Core\Foundation\Collection\ListItemInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

class Pagination extends Pagerfanta
{
    /** @var \Concrete\Core\Foundation\Collection\ListItemInterface  */
    protected $list;

    public function __construct(ListItemInterface $itemList, AdapterInterface $adapter) {
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