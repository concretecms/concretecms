<?php
namespace Concrete\Core\Express;

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
        if (in_array(strtolower($direction), array('asc', 'desc'))) {
            $this->query->orderBy($column, $direction);
        } else {
            throw new \Exception(t('Invalid SQL in order by'));
        }
    }

    public function executeGetResults()
    {
        $result = $this->query->getQuery()->getResult();

        return $result;
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
