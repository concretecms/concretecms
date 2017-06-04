<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Http\Request;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\ItemList\NextPreviousItemListInterface;
use Concrete\Core\Search\Pagination\Adapter\NextPreviousAdapter;
use Concrete\Core\Search\Pagination\View\NextPreviousViewRenderer;
use Concrete\Core\Support\Facade\Facade;
use Pagerfanta\Pagerfanta;

class NextPreviousPagination extends Pagination
{

    protected $list;
    protected $app;
    protected $request;

    public function __construct(NextPreviousItemListInterface $itemList)
    {
        $adapter = new NextPreviousAdapter($itemList);
        $this->list = $itemList;
        $this->app = Facade::getFacadeApplication();
        $this->request = Request::createFromGlobals();
        $this->handleQueryOffsets();
        return Pagerfanta::__construct($adapter);
    }

    private function handleQueryOffsets()
    {
        if ($this->list->getSearchRequest()) {
            $data = $this->list->getSearchQuery()->getSearchRequest();
        } else {
            $data = $this->request->query->all();
        }
        $this->list->filterQueryByOffset($this->list->getQueryObject(), $data);
    }

    public function renderView($driver = 'application', $arguments = array())
    {
        $manager = $this->app->make('manager/view/pagination/pager');
        $driver = $manager->driver($driver);

        $renderer = new NextPreviousViewRenderer($this, $driver);
        return $renderer->render($arguments);
    }


    public function haveToPaginate()
    {
        return true;
    }

    public function hasNextPage()
    {
        return true;
    }

    public function hasPreviousPage()
    {
        return true;
    }

    public function getCurrentPageResults()
    {
        return Pagerfanta::getCurrentPageResults();
    }

}
