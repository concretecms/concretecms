<?php
namespace Concrete\Core\Search;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Http\Request;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\ItemList\Pager\Manager\PagerManagerInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractSearchProvider implements ProviderInterface, SessionQueryProviderInterface
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setSessionCurrentQuery(Query $query)
    {
        $this->session->set('search/' . $this->getSessionNamespace() . '/query', $query);
    }

    public function clearSessionCurrentQuery()
    {
        $this->session->remove('search/' . $this->getSessionNamespace() . '/query');
    }

    public function getAllColumnSet()
    {
        $columnSet = new Set();
        foreach ($this->getAvailableColumnSet()->getColumns() as $column) {
            $columnSet->addColumn($column);
        }
        foreach ($this->getCustomAttributeKeys() as $ak) {
            $columnSet->addColumn(new AttributeKeyColumn($ak));
        }

        return $columnSet;
    }

    public function getSessionCurrentQuery()
    {
        $variable = 'search/' . $this->getSessionNamespace() . '/query';
        if ($this->session->has($variable)) {
            return $this->session->get($variable);
        }
    }

    public function getSearchResultFromQuery(Query $query)
    {
        $list = $this->getItemList();
        foreach ($query->getFields() as $field) {
            $field->filterList($list);
        }

        $list->disableAutomaticSorting(); // We don't need the automatic sorting found in the item list. it fires too late.

        $columns = $query->getColumns();
        if (is_object($columns)) {
            $column = $columns->getDefaultSortColumn();
            $list->sortBySearchColumn($column);
        } else {
            $columns = $this->getDefaultColumnSet();
        }

        $request = $list->getSearchRequest();
        if ($request) {
            $data = $request->getSearchRequest();
        } else {
            $data = \Request::getInstance()->query->all();
        }

        if (isset($data[$list->getQuerySortColumnParameter()])) {
            $value = $data[$list->getQuerySortColumnParameter()];
            $sortColumn = $columns->getColumnByKey($value);

            if (isset($data[$list->getQuerySortDirectionParameter()])) {
                $direction = $data[$list->getQuerySortDirectionParameter()];
            } else{
                $direction = $sortColumn->getColumnDefaultSortDirection();
            }

            $sortColumn->setColumnSortDirection($direction);
            $list->sortBySearchColumn($sortColumn, $direction);
        }

        if ($list instanceof PagerProviderInterface) {
            $manager = $list->getPagerManager();
            $manager->sortListByCursor($list, $list->getActiveSortDirection());
        }

        $list->setItemsPerPage($query->getItemsPerPage());
        $result = $this->createSearchResultObject($columns, $list);
        $result->setQuery($query);

        return $result;
    }

    /**
     * Gets items per page from the current preset or from the session.
     *
     * @return int
     */
    public function getItemsPerPage()
    {
        $sessionQuery = $this->getSessionCurrentQuery();
        if ($sessionQuery instanceof Query) {
            return $sessionQuery->getItemsPerPage();
        }
    }

    /**
     * @return array
     */
    public function getItemsPerPageOptions()
    {
        return [10, 25, 50, 100];
    }
}
