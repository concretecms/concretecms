<?php
namespace Concrete\Core\Search\ItemList;

use Doctrine\DBAL\Logging\EchoSQLLogger;

abstract class EntityItemList extends ItemList
{
    /** @var \Doctrine\ORM\QueryBuilder
     * @since 5.7.5
     */
    protected $query;

    /**
     * @return \Doctrine\ORM\EntityManager
     * @since 5.7.5
     */
    abstract public function getEntityManager();

    /**
     * @since 5.7.5
     */
    abstract public function createQuery();

    /**
     * @since 5.7.5
     */
    public function __construct()
    {
        $this->query = $this->getEntityManager()->createQueryBuilder();
        $this->createQuery();
    }

    /**
     * @since 5.7.5.2
     */
    public function getQueryObject()
    {
        return $this->query;
    }

    /**
     * @since 8.0.0
     */
    public function finalizeQuery(\Doctrine\ORM\QueryBuilder $query)
    {
        return $query;
    }

    /**
     * @since 8.0.0
     */
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
