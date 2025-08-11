<?php

namespace Concrete\Core\Search\Query;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueryFactory
 *
 * Responsible for creating Concrete\Core\Entity\Search\Query objects from different sources, which can then be
 * persisted, or turned into active result objects.
 *
 * @package Concrete\Core\Search\Query
 */
class QueryFactory
{

    /**
     * @param $method
     * @return array
     */
    private function getRequestData($request, $method): array
    {
        $vars = $method == Request::METHOD_POST ? $request->request->all() : $request->query->all();
        return $vars;
    }

    /**
     * Creates the default query object for a particular search provider. Pre-loaded search fields can be added to
     * filter the query.
     *
     * @param ProviderInterface $searchProvider
     * @param Request $request
     * @param FieldInterface[]
     * @param string $method
     * @return Query
     */
    public function createQuery(
        ProviderInterface $searchProvider,
        $fields = []
    )
    {
        $query = new Query();
        $set = $searchProvider->getDefaultColumnSet();

        $query->setFields($fields);
        $query->setColumns($set);
        $query->setItemsPerPage($searchProvider->getItemsPerPage());

        return $query;
    }

    /**
     * Creates a Query object from the request of the standard Advanced Search dialog. This is the dialog that includes
     * the stackable filters, customizable columns, items per page, etc...
     *
     * @param ProviderInterface $searchProvider
     * @param Request $request
     * @param string $method
     * @return Query
     */
    public function createFromAdvancedSearchRequest(ProviderInterface $searchProvider, Request $request, $method = Request::METHOD_POST)
    {
        $defaultColumnSet = $searchProvider->getDefaultColumnSet();
        $query = new Query();
        $vars = $this->getRequestData($request, $method);
        $fields = $searchProvider->getFieldManager()->getFieldsFromRequest($vars);

        $set = $searchProvider->getBaseColumnSet();
        $available = $searchProvider->getAvailableColumnSet();

        if (is_array($vars['column'] ?? null)) {
            foreach ($vars['column'] as $key) {
                $column = $available->getColumnByKey($key);
                if ($column) {
                    $set->addColumn($column);
                }
            }
        }
        if ($set->getColumns() === []) {
            foreach ($defaultColumnSet->getColumns() as $column) {
                $set->addColumn($column);
            }
        }
        $sort = empty($vars['fSearchDefaultSort']) ? null : $available->getColumnByKey($vars['fSearchDefaultSort']);
        if ($sort === null) {
            $set->setDefaultSortColumn($defaultColumnSet->getDefaultSortColumn(), $defaultColumnSet->getDefaultSortColumn()->getColumnSortDirection());
        } else {
            $set->setDefaultSortColumn($sort, $vars['fSearchDefaultSortDirection'] ?? 'asc');
        }
        $query->setFields($fields);
        $query->setColumns($set);
        $itemsPerPage = is_numeric($vars['fSearchItemsPerPage'] ?? null) ? (int) $vars['fSearchItemsPerPage'] : 0;
        if ($itemsPerPage > 0) {
            $query->setItemsPerPage($itemsPerPage);
        }

        return $query;
    }

    /**
     * Creates a query object from a saved search. You could easily just call `getQuery` on the
     * preset directly; this is mainly here for code purity.
     *
     * @param SavedSearch $preset
     * @return mixed
     */
    public function createFromSavedSearch(SavedSearch $preset)
    {
        return $preset->getQuery();
    }


}
