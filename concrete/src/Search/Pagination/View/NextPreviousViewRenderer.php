<?php

namespace Concrete\Core\Search\Pagination\View;
use Concrete\Core\Search\Pagination\NextPreviousPagination;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\View\ViewInterface;
use \Core;
class NextPreviousViewRenderer extends ViewRenderer
{

    public function __construct(NextPreviousPagination $pagination, ViewInterface $paginationView)
    {
        $this->view = $paginationView;
        $this->pagination = $pagination;
    }

    protected function getRouteCollectionFunction()
    {
        if (!$this->routeCollectionFunction) {
            $urlHelper = Core::make('helper/url');
            $list = $this->pagination->getItemListObject();
            $this->routeCollectionFunction = function ($page) use ($list, $urlHelper) {
                $args = array(
                    $list->getQuerySortColumnParameter() => $list->getActiveSortColumn(),
                    $list->getQuerySortDirectionParameter() => $list->getActiveSortDirection(),
                );

                $values = $list->getQueryOffsetNextPageValues($this->pagination);
                foreach($values as $column => $value) {
                    $args[$list->getQueryOffsetNextPageParameter($column)] = urlencode($value);
                }

                $url = $urlHelper->setVariable($args);
                return h($url);
            };
        }

        return $this->routeCollectionFunction;
    }

}
