<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList as AbstractItemList;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Core;
use Page;

class Pagination extends Pagerfanta
{
    /** @var \Concrete\Core\Search\ItemList\ItemList  */
    protected $list;

    public function __construct(AbstractItemList $itemList, AdapterInterface $adapter)
    {
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
     * This is a convenience method that does the following: 1. it grabs the pagination/view service (which by default
     * is bootstrap 3) 2. it sets up URLs to start with the pass of the current page, and 3. it uses the default
     * item list query string parameter for paging. If you need more custom functionality you should consider
     * using the Pagerfanta\View\ViewInterface objects directly.
     * @return string
     */
    public function renderDefaultView()
    {
        $v = Core::make('pagination/view');
        $list = $this->list;
        $html = $v->render(
            $this,
            function ($page) use ($list) {
                $qs = Core::make('helper/url');
                $url = $qs->setVariable($list->getQueryPaginationPageParameter(), $page);
                return $url;
            },
            array(
                'prev_message' => tc('Pagination', '&larr; Previous'),
                'next_message' => tc('Pagination', 'Next &rarr;'),
                'active_suffix' => '<span class="sr-only">' . tc('Pagination', '(current)') . '</span>'
            )
        );
        return $html;
    }

    public function getCurrentPageResults()
    {
        $this->list->debugStart();

        $results = parent::getCurrentPageResults();

        $this->list->debugStop();

        $return = array();
        foreach ($results as $result) {
            $r = $this->list->getResult($result);
            if ($r != null) {
                $return[] = $r;
            }
        }

        return $return;
    }
}
