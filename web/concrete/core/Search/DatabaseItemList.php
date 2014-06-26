<?
namespace Concrete\Core\Search;
use Database;
use Doctrine\DBAL\Logging\EchoSQLLogger;

abstract class DatabaseItemList implements ListItemInterface
{

    protected $sortColumnParameter = 'ccm_order_by';
    protected $sortDirectionParameter = 'ccm_order_by_direction';
    protected $paginationPageParameter = 'ccm_paging_p';
    protected $sortBy;
    protected $sortByDirection;

    /** @var \Doctrine\DBAL\Query\QueryBuilder */
    protected $query;

    /** @var \Concrete\Core\Search\Pagination\Pagination  */
    protected $pagination;

    public function __construct(StickyRequest $req = null)
    {
        $this->query = Database::get()->createQueryBuilder();

        // create the initial query.
        $this->createQuery();

        // setup the default sorting based on the request.
        $this->setupAutomaticSorting($req);
    }

    public function getQueryObject()
    {
        return $this->query;
    }

    /** Returns a full array of results. */
    public function getResults()
    {
        $results = array();
        foreach($this->query->execute()->fetchAll() as $result) {
            $r = $this->getResult($result);
            if ($r != null) {
                $results[] = $r;
            }
        }
        return $results;
    }

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

    abstract protected function createPaginationObject();

    public function filter($field, $value, $comparison = '=')
    {
        $this->query->andWhere(implode(' ', array(
           $field, $comparison, $this->query->createNamedParameter($value)
        )));
    }

    public function sortBy($field, $direction = 'asc')
    {
        $this->sortBy = $field;
        $this->sortByDirection = $direction;
        $this->query->orderBy($field, $direction);
    }

    public function getActiveSortColumn()
    {
        return $this->sortBy;
    }

    public function isActiveSortColumn($field)
    {
        return $this->sortBy == $field;
    }

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