<?php

namespace Concrete\Core\Api;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Search\ItemList\EntityItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class IntegrationList extends EntityItemList
{

    protected $entityManager;
    protected $itemsPerPage = 20;
    protected $autoSortColumns = ['c.name'];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
        $this->sortBy('c.name', 'asc');
    }

    public function filterByKeywords($keywords)
    {
        $this->query->andWhere(
            $this->query->expr()->like('c.name', ':name')
        );
        $this->query->setParameter('name', '%' . $keywords . '%');
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function createQuery()
    {
        $this->query->select('c')->from(Client::class, 'c');
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineORMAdapter($this->query, function ($query) {
            $query->select('count(distinct c.identifier)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);
        return $pagination;
    }

    public function getResult($result)
    {
        return $result;
    }

    public function getTotalResults(): int
    {
        return $this->query->select('count(c)')->setMaxResults(1)->getQuery()->getSingleScalarResult();
    }

}
