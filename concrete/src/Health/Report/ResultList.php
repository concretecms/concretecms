<?php

namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Search\ItemList\EntityItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class ResultList extends EntityItemList
{

    protected $entityManager;
    protected $itemsPerPage = 20;
    protected $autoSortColumns = ['c.name'];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
        $this->sortBy('process.dateStarted', 'desc');
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function createQuery()
    {
        $this->query->select('result, process')->from(Result::class, 'result')
            ->innerJoin('result.process', 'process');
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineORMAdapter($this->query, function ($query) {
            $query->select('count(result)')->setMaxResults(1);
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
        return $this->query->select('count(result)')->setMaxResults(1)->getQuery()->getSingleScalarResult();
    }

}
