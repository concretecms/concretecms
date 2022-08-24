<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Command\Batch;
use Concrete\Core\Entity\Command\Process;
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

    public function createOrGetRunner(Batch $batch): Runner
    {
        $process = $this->entityManager->getRepository(Process::class)
            ->findOneByBatch($batch);
        if ($process) {
            $result = $this->entityManager->getRepository(Result::class)
                ->findOneByProcess($process);
            if (!$result) {
                $result = new Result();
                $result->setProcess($process);
                $this->entityManager->persist($result);
                $this->entityManager->flush();
            }

            $runner = new Runner($this->entityManager, $result);
            return $runner;

        } else {
            throw new \RuntimeException(t('Unable to determine process from batch: %s', $batch->getId()));
        }
    }



}
