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

    protected $baseURL;

    /**
     * @return mixed
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }

    /**
     * @param mixed $baseURL
     */
    public function setBaseURL($baseURL)
    {
        $this->baseURL = $baseURL;
    }

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

    public function getItemListObject()
    {
        return $this->list;
    }

    /**
     * This is a convenience method that does the following: 1. it grabs the pagination/view service (which by default
     * is bootstrap 3) 2. it sets up URLs to start with the pass of the current page, and 3. it uses the default
     * item list query string parameter for paging. If you need more custom functionality you should consider
     * using the Pagerfanta\View\ViewInterface objects directly.
     *
     * @param array
     *
     * @return string
     */
    public function renderDefaultView($arguments = array())
    {
        return $this->renderView('application', $arguments);
    }

    public function renderView($driver = 'application', $arguments = array())
    {
        $manager = Core::make('manager/view/pagination');
        $driver = $manager->driver($driver);
        $v = Core::make('\Concrete\Core\Search\Pagination\View\ViewRenderer', array($this, $driver));

        return $v->render($arguments);
    }

    public function getRouteCollectionFunction()
    {
        $urlHelper = Core::make('helper/url');;
        $list = $this->getItemListObject();
        // Note: We had been using the URL library per a pull request by someone, but it
        // was breaking pagination in the sitemap flat view so that has been reverted.
        $routeCollectionFunction = function ($page) use ($list, $urlHelper) {
            $args = array(
                $list->getQueryPaginationPageParameter() => $page,
                $list->getQuerySortColumnParameter() => $list->getActiveSortColumn(),
                $list->getQuerySortDirectionParameter() => $list->getActiveSortDirection(),
            );
            if ($this->baseURL) {
                $url = $urlHelper->setVariable($args, false, $this->baseURL);
            } else {
                $url = $urlHelper->setVariable($args);
            }
            return h($url);
        };

        return $routeCollectionFunction;
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
