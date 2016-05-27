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
            // Note: We had been using the URL library per a pull request by someone, but it
            // was breaking pagination in the sitemap flat view so that has been reverted.
            $this->routeCollectionFunction = function ($page) use ($list, $urlHelper) {
                $args = array(
                    $list->getQueryPaginationPageParameter() => $page,
                    $list->getQuerySortColumnParameter() => $list->getActiveSortColumn(),
                    $list->getQuerySortDirectionParameter() => $list->getActiveSortDirection(),
                );
                $url = $urlHelper->setVariable($args);
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