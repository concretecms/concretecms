<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList as AbstractItemList;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Core;

class Pagination extends Pagerfanta
{
    /** @var \Concrete\Core\Search\ItemList\ItemList  */
    protected $list;

    public function __construct(AbstractItemList $itemList, AdapterInterface $adapter) {
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

    /**
     * return @\Pagerfanta\View\ViewInterface
     */
    public function getView()
    {
        $v = Core::make('pagination/view');
        return $v;
    }

    public function getCurrentPageResults()
    {
        $this->list->debugStart();

        $results = parent::getCurrentPageResults();

        $this->list->debugStop();

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