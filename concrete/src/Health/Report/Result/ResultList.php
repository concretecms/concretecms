<?php

namespace Concrete\Core\Health\Report\Result;

use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Search\ItemList\EntityItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationFactory;
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
        $this->sortBy('result.dateStarted', 'desc');
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function createQuery()
    {
        $this->query->select('result')->from(Result::class, 'result')
            ->andWhere('result.dateCompleted is not null');
    }

    public function filterByTask(Task $task)
    {
        $this->query->andWhere('result.task = :task');
        $this->query->setParameter('task', $task);
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

    /**
     * Just a useful helper function
     *
     * @return Result|null
     */
    public static function getLatestResult(Task $task = null): ?Result
    {
        $list = app(self::class);
        if ($task) {
            $list->filterbyTask($task);
        }
        $list->setItemsPerPage(1);
        $pagination = app(PaginationFactory::class)->createPaginationObject($list);
        /**
         * @var $pagination Pagination
         */
        if ($pagination->getTotalResults() > 0) {
            return $pagination->getCurrentPageResults()[0];
        }
        return null;
    }

}
