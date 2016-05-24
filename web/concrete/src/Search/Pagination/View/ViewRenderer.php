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
    }

    protected function getRouteCollectionFunction()
    {
        if (!$this->routeCollectionFunction) {
            $urlHelper = Core::make('helper/url');;
            $list = $this->pagination->getItemListObject();
            $this->routeCollectionFunction = function ($page) use ($list, $urlHelper) {
                $url = $urlHelper->setVariable($list->getQueryPaginationPageParameter(), $page);
                return h($url);
            };
        }

        return $this->routeCollectionFunction;
    }

    /**
     * @return string
     */
    public function render($args = array())
    {
        return $this->view->render(
            $this->pagination,
            $this->getRouteCollectionFunction(),
            array_merge($this->view->getArguments(), $args)
        );
    }
}
