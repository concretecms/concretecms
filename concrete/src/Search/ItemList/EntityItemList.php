<?php
namespace Concrete\Core\Search\ItemList;

use Doctrine\DBAL\Logging\EchoSQLLogger;

abstract class EntityItemList extends ItemList
{
    /** @var \Doctrine\ORM\QueryBuilder */
    protected $query;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    abstract public function getEntityManager();

    abstract public function createQuery();

    public function __construct()
    {
        $this->query = $this->getEntityManager()->createQueryBuilder();
        $this->createQuery();
    }

    public function getQueryObject()
    {
        return $this->query;
    }

    public function finalizeQuery(\Doctrine\ORM\QueryBuilder $query)
    {
        return $query;
    }

    public function deliverQueryObject()
    {
        $this->setupAutomaticSorting();
        $query = clone $this->query;
        $query = $this->finalizeQuery($query);
        return $query;
    }

    public function debugStart()
    {
        if ($this->isDebugged()) {
            $this->getEntityManager()
                ->getConnection()
                ->getConfiguration()
                ->setSQLLogger(new EchoSQLLogger());
        }
    }

    public function debugStop()
    {
        if ($this->isDebugged()) {
            $this->getEntityManager()
                ->getConnection()
                ->getConfiguration()
                ->setSQLLogger(null);
        }
    }

    public function executeGetResults()
    {
        return $this->deliverQueryObject()->getQuery()->getResult();
    }

    protected function executeSortBy($column, $direction = 'asc')
    {
        if (in_array(strtolower($direction), array('asc','desc'))) {
            $this->query->orderBy($column, $direction);
        }
    }



}
