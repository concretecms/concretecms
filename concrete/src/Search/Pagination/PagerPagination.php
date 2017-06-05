<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\Pagination\Adapter\PagerAdapter;
use Concrete\Core\Search\Pagination\View\PagerViewRenderer;
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
        $variables = $factory->getRequestedVariables();
        foreach($variables as $variable) {
            $manager->filterByVariable($variable, $itemList);
        }
        return Pagerfanta::__construct($adapter);
    }

    public function renderView($driver = 'application', $arguments = array())
    {
        $manager = $this->app->make('manager/view/pagination/pager');
        $driver = $manager->driver($driver);

        $renderer = new PagerViewRenderer($this, $driver);
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
