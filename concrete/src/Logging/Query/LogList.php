<?php
namespace Concrete\Core\Logging\Query;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class LogList extends ItemList
{

    protected $autoSortColumns = array('queryTotal', 'query');
    protected $sortBy = 'queryTotal';
    protected $sortByDirection = 'desc';

    public function createQuery()
    {
        $this->query->select('query', 'count(*) as queryTotal')
            ->from('SystemDatabaseQueryLog', 'ql')
            ->groupBy('query')
            ->orderBy('queryTotal', 'desc');
    }

    /**
     * The total results of the query
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->select('count(ql.query)')->setMaxResults(1)->execute()->fetchColumn();
    }

    /**
     * Gets the pagination object for the query.
     * @return Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts()->select('count(distinct query)')
                ->from('SystemDatabaseQueryLog', 'ql')
                ->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);
        return $pagination;
    }

    /**
     * @param $queryRow
     * @return array
     */
    public function getResult($queryRow)
    {
        return $queryRow;
    }


}
