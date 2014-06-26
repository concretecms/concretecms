<?
namespace Concrete\Core\Search;
use Database;
use Doctrine\DBAL\Logging\EchoSQLLogger;

abstract class DatabaseItemList extends ItemList
{

    /** @var \Doctrine\DBAL\Query\QueryBuilder */
    protected $query;

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        return $query;
    }

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

    public function deliverQueryObject()
    {
        $query = clone $this->query;
        $query = $this->finalizeQuery($query);
        return $query;
    }

    public function executeGetResults()
    {
        return $this->deliverQueryObject()->execute()->fetchAll();
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