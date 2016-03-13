<?php

namespace Concrete\Core\Search\Pagination\View;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\View\ViewInterface;
use \Core;
use Request;
use URL;
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
            $request = Request::getInstance();
            $url = URL::to($request->getRequestUri());
            $query = $url->getQuery();
            $query->modify(array($list->getQueryPaginationPageParameter() => $page));
            $url = $url->setQuery($query);
            return (string) $url;
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