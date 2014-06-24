<?
namespace Concrete\Core\Search;
use Database;

abstract class DatabaseItemList implements ListItemInterface
{

    protected $sortColumnParameter = 'ccm_order_by';
    protected $sortDirectionParameter = 'ccm_order_by_direction';

    /** @var \Doctrine\DBAL\Query\QueryBuilder */
    protected $query;

    /** @var \Concrete\Core\Pagination\Pagination  */
    protected $pagination;

    public function __construct()
    {
        $this->query = Database::get()->createQueryBuilder();

        // create the initial query.
        $this->createQuery();

        // setup the default sorting based on the request.
        $this->setupAutomaticSorting();
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

    public function sortBy($field, $directon = 'asc')
    {
        $this->query->orderBy($field, $directon);
    }

    public function getQuerySortColumnParameter()
    {
        return $this->sortColumnParameter;
    }

    public function getQuerySortDirectionParameter()
    {
        return $this->sortDirectionParameter;
    }

    public function setupAutomaticSorting()
    {
        $req = \Request::getInstance();
        $direction = 'asc';
        if ($req->query->has($this->getQuerySortDirectionParameter())) {
            $direction = $req->query->get($this->getQuerySortDirectionParameter());
        }
        if ($req->query->has($this->getQuerySortColumnParameter())) {
            $this->query->orderBy($req->query->get($this->getQuerySortColumnParameter()), $direction);
        }
    }
}