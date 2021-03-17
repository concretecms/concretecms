<?php
namespace Concrete\Core\Search\Result;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResultFactory
 *
 * Responsible for creating Result objects from different sources, which are used in search views.
 *
 * @package Concrete\Core\Search\Result
 */
class ResultFactory
{

    public function createFromQuery(ProviderInterface $searchProvider, Query $query)
    {
        $list = $searchProvider->getItemList();
        foreach ($query->getFields() as $field) {
            $field->filterList($list);
        }

        $columns = $query->getColumns();
        if (is_object($columns)) {
            $column = $columns->getDefaultSortColumn();
            $list->sortBySearchColumn($column);
        } else {
            $columns = $searchProvider->getDefaultColumnSet();
        }

        if ($list instanceof PagerProviderInterface) {
            $manager = $list->getPagerManager();
            $manager->sortListByCursor($list, $list->getActiveSortDirection());
        }
        
        $list->setItemsPerPage($query->getItemsPerPage());
        $result = $searchProvider->createSearchResultObject($columns, $list);
        $result->setQuery($query);

        return $result;
    }


}
