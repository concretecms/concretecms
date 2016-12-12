<?php
namespace Concrete\Core\Search\ItemList;

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
        $query = clone $this->query;
        $query = $this->finalizeQuery($query);
        return $query;
    }

}
