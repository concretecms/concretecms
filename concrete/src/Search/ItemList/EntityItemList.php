<?php
namespace Concrete\Core\Search\ItemList;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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

}
