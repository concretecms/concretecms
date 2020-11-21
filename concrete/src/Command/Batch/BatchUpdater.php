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
    protected $app;

    /**
     * BatchUpdater constructor.
     * @param Application $app
     * @param ProcessUpdater $processUpdater
     */
    public function __construct(Application $app)
    {
        // Yes, I know the dependencies are actually entity manager and processupdater but don't change it. This fires too early
        // in the booting process and it breaks uninstalled concrete5 sites entirely because it tries to boot
        // the database with credentials that don't exist.

        $this->app = $app;
    }

    public function checkBatchProcessForClose(string $batchId)
    {
        $entityManager = $this->app->make(EntityManager::class);
        $processUpdater = $this->app->make(ProcessUpdater::class);
        $batch = $entityManager->find(BatchEntity::class, $batchId);
        if ($batch) {
            $entityManager->refresh($batch);
            if ($batch->getPendingJobs() < 1) {
                $process = $entityManager->getRepository(Process::class)->findOneByBatch($batch);
                if ($process) {
                    $processUpdater->closeProcess($process);
                }
            }
        }
    }

    public function updateJobs(string $batchId, string $column, int $jobs)
    {
        $entityManager = $this->app->make(EntityManager::class);
        if (!in_array($column, [self::COLUMN_TOTAL, self::COLUMN_FAILED, self::COLUMN_PENDING])) {
            throw new \Exception(t('Invalid column passed to BatchUpdater::updateJobs: %s', $column));
        }
        if ($jobs < 0) {
            $jobs = abs($jobs);
            $query = $entityManager->createQuery("update \Concrete\Core\Entity\Command\Batch b set b.$column = b.$column - :jobs where b.id = :batch");
        } else {
            $query = $entityManager->createQuery("update \Concrete\Core\Entity\Command\Batch b set b.$column = b.$column + :jobs where b.id = :batch");
        }
        $query->setParameter('batch', $batchId);
        $query->setParameter('jobs', $jobs);
        $query->execute();
    }

}
