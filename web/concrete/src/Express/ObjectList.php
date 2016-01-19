<?php
namespace Concrete\Core\Express;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Search\ItemList\EntityItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class ObjectList extends EntityItemList
{
    protected $entity;
    protected $objectManager;

    public function __construct(ObjectManager $objectManager, Entity $entity)
    {
        $this->objectManager = $objectManager;
        $this->entity = $entity;
        parent::__construct();
    }

    public function getEntityManager()
    {
        return $this->objectManager->getEntityManager();
    }

    public function createQuery()
    {
        $class = $this->objectManager->getClassName($this->entity);
        $this->query->select('o')->from($class, 'o');
    }

    protected function executeSortBy($column, $direction = 'asc')
    {
        return false; // This is handled by finalizeQuery below.
    }

    public function executeGetResults()
    {
        $result = $this->deliverQueryObject()->getQuery()->getResult();
        return $result;
    }

    public function finalizeQuery(\Doctrine\ORM\QueryBuilder $query)
    {
        if (isset($this->sortBy) && isset($this->sortByDirection)) {
            $category = $this->entity->getAttributeKeyCategory();
            $key = $category->getAttributeKeyByHandle(substr($this->sortBy, 3));
            $handle = $key->getAttributeKeyHandle();
            $query->leftJoin(sprintf('o.%s', $handle), 'sort');
            $query->orderBy('sort.value', $this->sortByDirection);
        }
        return $query;
    }

    public function getResult($result)
    {
        return $result;
    }

    public function checkPermissions($mixed)
    {
        return true;
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

    public function sortByAttribute(Key $key, $direction = 'asc')
    {
        $this->sortBy = $key;
        $this->sortByDirection = $direction;
    }

    public function getTotalResults()
    {
        return $this->query->select('count(distinct o.id)')->getQuery()->getSingleScalarResult();
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineORMAdapter($this->query, function ($query) {
            $query->select('count(distinct o.id)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }
}
