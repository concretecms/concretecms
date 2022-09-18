<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Health\Report\Result;
use Doctrine\ORM\EntityManager;

class RunnerFactory
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createRunnerFromResultId(string $resultId): Runner
    {
        $result = $this->entityManager->getRepository(Result::class)
            ->findOneById($resultId);
        $runner = new Runner($this->entityManager, $result);
        return $runner;
    }



}
