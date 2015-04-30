<?php

namespace Concrete\Core\Search\Pagination\View;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\View\ViewInterface;
use \Core;
class ViewRenderer
{

    protected $view;
    protected $pagination;
    protected $routeCollectionFunction;

    public function __construct(Pagination $pagination, ViewInterface $paginationView)
    {
        $this->view = $paginationView;
        $this->pagination = $pagination;
        $list = $pagination->getItemListObject();
        $this->routeCollectionFunction = function ($page) use ($list) {
            $qs = Core::make('helper/url');
            $url = $qs->setVariable($list->getQueryPaginationPageParameter(), $page);
            return $url;
        };
    }

    protected function getRouteCollectionFunction()
    {
        return $this->routeCollectionFunction;
    }

    /**
     * @return string
     */
    public function render($args = array())
    {
        return $this->view->render(
            $this->pagination,
            $this->routeCollectionFunction,
            array_merge($this->view->getArguments(), $args)
        );
    }
}