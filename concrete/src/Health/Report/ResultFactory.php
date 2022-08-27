<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Command\Batch;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Entity\Health\Report\Result;
use Doctrine\ORM\EntityManager;

class ResultFactory
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createResult(Task $reportTask): Result
    {
        $result = new Result();
        $result->setName($reportTask->getController()->getName());
        $result->setDateStarted(time());
        $result->setTask($reportTask);
        $this->entityManager->persist($result);
        $this->entityManager->flush();

        return $result;
    }

}
