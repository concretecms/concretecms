<?
namespace Concrete\Core\Search;

abstract class ItemList
{
    protected $sortColumnParameter = 'ccm_order_by';
    protected $sortDirectionParameter = 'ccm_order_by_direction';
    protected $paginationPageParameter = 'ccm_paging_p';
    protected $sortBy;
    protected $sortByDirection;
    protected $autoSortColumns = array();

    abstract protected function createPaginationObject();
    abstract protected function executeSortBy($field, $direction = 'asc');
    abstract public function executeGetResults();
    abstract public function getResult($mixed);

    public function sortBy($field, $direction = 'asc')
    {
        $this->sortBy = $field;
        $this->sortByDirection = $direction;
        $this->executeSortBy($field, $direction);
    }

    /** Returns a full array of results. */
    public function getResults()
    {
        $results = array();
        $executeResults = $this->executeGetResults();
        foreach($executeResults as $result) {
            $r = $this->getResult($result);
            if ($r != null) {
                $results[] = $r;
            }
        }

        return $results;
    }

    public function getActiveSortColumn()
    {
        return $this->sortBy;
    }

    public function isActiveSortColumn($field)
    {
        return $this->sortBy == $field;
    }    /** @var \Concrete\Core\Search\Pagination\Pagination  */
    protected $pagination;


    public function getActiveSortDirection()
    {
        return $this->sortByDirection;
    }

    public function getQuerySortColumnParameter()
    {
        return $this->sortColumnParameter;
    }

    public function getQueryPaginationPageParameter()
    {
        return $this->paginationPageParameter;
    }

    public function getQuerySortDirectionParameter()
    {
        return $this->sortDirectionParameter;
    }

    /**
     * Returns the total results in this item list.
     * @return int
     */
    abstract public function getTotalResults();

    /**
     * @return PermissionablePagination|Pagination
     */
    public function getPagination()
    {
        $pagination = $this->createPaginationObject();
        $query = \Request::getInstance()->query;
        if ($query->has($this->getQueryPaginationPageParameter())) {
            $page = intval($query->get($this->getQueryPaginationPageParameter()));
            $pagination->setCurrentPage($page);
        }
        return $pagination;
    }

    /**
     *
     * @param StickyRequest $request
     */
    public function setupAutomaticSorting(StickyRequest $request = null)
    {
        // First, we check to see if there are any sortable attributes we can add to the
        // auto sort columns.
        if (is_callable(array($this->attributeClass, 'getList'))) {
            $l = call_user_func(array($this->attributeClass, 'getList'));
            foreach($l as $ak) {
                $this->autoSortColumns[] = 'ak_' . $ak->getAttributeKeyHandle();
            }
        }

        // now we check to see if we should setup sorting by a sticky search request.
        if ($request) {
            $data = $request->getSearchRequest();
        } else {
            $data = \Request::getInstance()->query->all();
        }
        $direction = 'asc';
        if (isset($data[$this->getQuerySortDirectionParameter()])) {
            $direction = $data[$this->getQuerySortDirectionParameter()];
        }
        if (isset($data[$this->getQuerySortColumnParameter()])) {
            $value = $data[$this->getQuerySortColumnParameter()];
            if (in_array($value, $this->autoSortColumns)) {
                $this->sortBy($value, $direction);
            }
        }
    }
}