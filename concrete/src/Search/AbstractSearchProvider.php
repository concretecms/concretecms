<?php
namespace Concrete\Core\Search;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Set;
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
        if (!$list->getActiveSortColumn()) {
            $columns = $query->getColumns();
            if (is_object($columns)) {
                $column = $columns->getDefaultSortColumn();
                $list->sanitizedSortBy($column->getColumnKey(), $column->getColumnDefaultSortDirection());
            } else {
                $columns = $this->getDefaultColumnSet();
            }
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
        // Note: this shouldn't be here. This should be a general function that
        // checks the current query to see if it's part of a preset. This is file manager
        // specific functionality in a non-file-manager-specific class.
        // @TODO fix this
        $searchRequest = new StickyRequest('file_manager_folder');
        $searchParams = $searchRequest->getSearchRequest();
        $node = empty($searchParams['folder']) ? null : Node::getByID($searchParams['folder']);

        if ($node instanceof SearchPreset) {
            $searchObj = $node->getSavedSearchObject();

            return $searchObj->getQuery()->getItemsPerPage();
        } else {
            $sessionQuery = $this->getSessionCurrentQuery();

            if ($sessionQuery instanceof Query) {
                return $sessionQuery->getItemsPerPage();
            }
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
