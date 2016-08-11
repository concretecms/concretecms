<?php
namespace Concrete\Core\Search;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Result\Result as SearchResult;
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

    public function getSessionCurrentQuery()
    {
        $variable = 'search/'. $this->getSessionNamespace() . '/query';
        if ($this->session->has($variable)) {
            return $this->session->get($variable);
        }
    }

    public function getSearchResultFromQuery(Query $query)
    {
        $list = $this->getItemList();
        foreach($query->getFields() as $field) {
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
        $result = $this->createSearchResultObject($columns, $list);
        $result->setQuery($query);
        return $result;
    }



}
