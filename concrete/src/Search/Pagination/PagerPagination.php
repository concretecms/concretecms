<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\PagerManagerInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\Pagination\Adapter\PagerAdapter;
use Concrete\Core\Search\Pagination\View\PagerManager;
use Concrete\Core\Search\Pagination\View\PagerViewRenderer;
use Concrete\Core\Search\Pagination\View\ViewRenderer;
use Concrete\Core\Support\Facade\Facade;
use Pagerfanta\Pagerfanta;

class PagerPagination extends Pagination
{

    protected $list;
    protected $app;
    protected $request;

    public function __construct(PagerProviderInterface $itemList)
    {
        $adapter = new PagerAdapter($itemList);
        $this->list = $itemList;
        $this->app = Facade::getFacadeApplication();

        $manager = $itemList->getPagerManager();

        $factory = $itemList->getPagerVariableFactory();
        $start = $factory->getCurrentCursor();
        if ($start) {
            $manager->displaySegmentAtCursor($start, $itemList);
        }

        return Pagerfanta::__construct($adapter);
    }

    public function renderView($driver = 'application', $arguments = array())
    {
        $manager = $this->app->make('manager/view/pagination/pager');
        $driver = $manager->driver($driver);
        $renderer = new ViewRenderer($this, $driver);
        return $renderer->render($arguments);
    }

    public function getRouteCollectionFunction()
    {
        $urlHelper = $this->app->make('helper/url');
        /**
         * @var $list PagerProviderInterface
         */
        $list = $this->getItemListObject();
        $routeCollectionFunction = function ($page) use ($list, $urlHelper) {
            $args = array();
            if ($list->getActiveSortColumn()) {
                $args[$list->getQuerySortColumnParameter()] = $list->getActiveSortColumn();
            }

            if ($list->getActiveSortDirection()) {
                $args[$list->getQuerySortDirectionParameter()] = $list->getActiveSortDirection();
            }

            /**
             * @var $manager PagerManagerInterface
             */
            $factory = $list->getPagerVariableFactory();
            if ($page == 2) {
                // next page
                $args[$factory->getCursorVariableName()] = $factory->getNextCursorValue($this);
            } else if ($page == 0) {
                $args[$factory->getCursorVariableName()] = $factory->getPreviousCursorValue($this);
            }

            if ($this->baseURL) {
                $url = $urlHelper->setVariable($args, false, $this->baseURL);
            } else {
                $url = $urlHelper->setVariable($args);
            }
            return h($url);

        };

        return $routeCollectionFunction;
    }

    public function haveToPaginate()
    {
        return $this->hasNextPage() || $this->hasPreviousPage();
    }

    public function hasNextPage()
    {
        return true;
    }

    public function hasPreviousPage()
    {
        $factory = $this->list->getPagerVariableFactory();
        return $factory->getCurrentCursor() != '';
    }

    public function getCurrentPageResults()
    {
        return Pagerfanta::getCurrentPageResults();
    }

}
