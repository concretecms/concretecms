<?
namespace Concrete\Core\Search;
use Database;
use Doctrine\DBAL\Logging\EchoSQLLogger;

abstract class DatabaseItemList extends ItemList
{

    /** @var \Doctrine\DBAL\Query\QueryBuilder */
    protected $query;

    abstract public function createQuery();

    final public function __construct(StickyRequest $req = null)
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

    public function executeGetResults()
    {
        return $this->query->execute()->fetchAll();
    }

    protected function executeSortBy($column, $direction = 'asc')
    {
        $this->query->orderBy($column, $direction);
    }

    public function filter($field, $value, $comparison = '=')
    {
        $this->query->andWhere(implode(' ', array(
           $field, $comparison, $this->query->createNamedParameter($value)
        )));
    }


}