<?php

namespace Concrete\Core\Command\Batch;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Process\ProcessUpdater;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Localization\Service\Date;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Command\Batch as BatchEntity;

class BatchUpdater
{

    const COLUMN_PENDING = 'pendingJobs';
    const COLUMN_TOTAL = 'totalJobs';
    const COLUMN_FAILED = 'failedJobs';

    /**
     * @var Application
     */
    protected $entityManager;

    /**
     * @var ProcessUpdater
     */
    protected $processUpdater;

    /**
     * BatchProgressUpdater constructor.
     * @param Application $app
     */
    public function __construct(EntityManager $entityManager, ProcessUpdater $processUpdater)
    {
        $this->entityManager = $entityManager;
        $this->processUpdater = $processUpdater;
    }

    public function checkBatchProcessForClose(string $batchId)
    {
        $batch = $this->entityManager->find(BatchEntity::class, $batchId);
        if ($batch) {
            $this->entityManager->refresh($batch);
            if ($batch->getPendingJobs() < 1) {
                $process = $this->entityManager->getRepository(Process::class)->findOneByBatch($batch);
                if ($process) {
                    $this->processUpdater->closeProcess($process);
                }
            }
        }
    }

    public function updateJobs(string $batchId, string $column, int $jobs)
    {
        if (!in_array($column, [self::COLUMN_TOTAL, self::COLUMN_FAILED, self::COLUMN_PENDING])) {
            throw new \Exception(t('Invalid column passed to BatchUpdater::updateJobs: %s', $column));
        }
        if ($jobs < 0) {
            $jobs = abs($jobs);
            $query = $this->entityManager->createQuery("update \Concrete\Core\Entity\Command\Batch b set b.$column = b.$column - :jobs where b.id = :batch");
        } else {
            $query = $this->entityManager->createQuery("update \Concrete\Core\Entity\Command\Batch b set b.$column = b.$column + :jobs where b.id = :batch");
        }
        $query->setParameter('batch', $batchId);
        $query->setParameter('jobs', $jobs);
        $query->execute();
    }

}
