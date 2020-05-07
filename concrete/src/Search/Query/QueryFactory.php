<?php
namespace Concrete\Core\Search\Query;

use Concrete\Core\Entity\Search\Query;
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
        $query = new Query();
        $vars = $method == Request::METHOD_POST ? $request->request->all() : $request->query->all();
        $fields = $searchProvider->getFieldManager()->getFieldsFromRequest($vars);

        $set = $searchProvider->getBaseColumnSet();
        $available = $searchProvider->getAvailableColumnSet();

        if (isset($vars['column']) && is_array($vars['column'])) {
            foreach ($vars['column'] as $key) {
                $set->addColumn($available->getColumnByKey($key));
            }
        }

        $sort = $available->getColumnByKey($vars['fSearchDefaultSort']);
        $set->setDefaultSortColumn($sort, $vars['fSearchDefaultSortDirection']);

        $query->setFields($fields);
        $query->setColumns($set);

        $itemsPerPage = $vars['fSearchItemsPerPage'];
        $query->setItemsPerPage($itemsPerPage);

        return $query;
    }


}
