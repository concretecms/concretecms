<?php

namespace Concrete\Core\Search\Pagination\View;
use Concrete\Core\Search\ItemList\Pager\Manager\PagerManagerInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\OffsetStartVariable;
use Concrete\Core\Search\ItemList\Pager\QueryString\PreviousOffsetStartVariable;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableInterface;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\View\ViewInterface;
use \Core;
class PagerViewRenderer extends ViewRenderer
{

    public function __construct(PagerPagination $pagination, ViewInterface $paginationView)
    {
        $this->view = $paginationView;
        $this->pagination = $pagination;
    }

    protected function getRouteCollectionFunction()
    {
        if (!$this->routeCollectionFunction) {
            $urlHelper = Core::make('helper/url');
            /**
             * @var $list PagerProviderInterface
             */
            $list = $this->pagination->getItemListObject();
            $this->routeCollectionFunction = function ($page) use ($list, $urlHelper) {
                $args = array(
                    $list->getQuerySortColumnParameter() => $list->getActiveSortColumn(),
                    $list->getQuerySortDirectionParameter() => $list->getActiveSortDirection(),
                );

                /**
                 * @var $manager PagerManagerInterface
                 */
                $manager = $list->getPagerManager();
                if ($page == 2) {
                    // next page
                    $values = $manager->getNextPageVariables($list, $this->pagination);
                    foreach($values as $value) {
                        $args[$value->getVariable()] = $value->getValue();
                    }

                    // And we also make sure to grab all the previous offset start variables
                    // so we'll be able to pass them in for paging backward.

                    $factory = $list->getPagerVariableFactory();
                    $variables = $factory->getRequestedVariables();
                    foreach($variables as $variable) {
                        if ($variable instanceof OffsetStartVariable) {
                            $previous = new PreviousOffsetStartVariable($variable->getName(), $variable->getValue());
                            $args[$previous->getVariable()] = $variable->getValue();
                        }
                    }
                } else if ($page == 0) {

                }

                $url = $urlHelper->setVariable($args);
                return h($url);

                /**
                 * @var $values VariableInterface[]
                 */

                // Now we parse all the next variables out of the string and we turn them into previous variables
                /*
                $factory = $list->getPagerVariableFactory();
                $variables = $factory->getRequestedVariables();
                foreach($variables as $variable) {
                    if ($variable instanceof NextVariable) {
                        $previous = new PreviousVariable($variable->getName(), $variable->getValue());
                        $args[$previous->getVariable()] = $variable->getValue();
                    }
                }
                */

            };
        }

        return $this->routeCollectionFunction;
    }

}
