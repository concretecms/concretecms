<?php

namespace Concrete\Core\Command\Batch;

use Concrete\Core\Application\Application;
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
     * @var Date
     */
    protected $dateService;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * BatchProgressUpdater constructor.
     * @param Application $app
     */
    public function __construct(Application $app, EntityManager $entityManager, Date $dateService, Repository $config)
    {
        $this->entityManager = $entityManager;
        $this->dateService = $dateService;
        $this->config = $config;
    }

    public function checkBatchProcessForClose(string $batchId)
    {
        $batch = $this->entityManager->find(BatchEntity::class, $batchId);
        if ($batch) {
            $this->entityManager->refresh($batch);
            if ($batch->getPendingJobs() < 1) {
                $process = $this->entityManager->getRepository(Process::class)->findOneByBatch($batch);
                if ($process) {
                    $process->setDateCompleted($this->dateService->toDateTime()->getTimestamp());
                    $this->entityManager->persist($process);
                    $this->entityManager->flush();
                    $this->clearOldProcesses();
                }
            }
        }
    }

    protected function clearOldProcesses()
    {
        $threshold = (int) $this->config->get('concrete.processes.delete_threshold');
        $now = new \DateTime();
        $now->sub(new \DateInterval('P' . $threshold . 'D'));
        $minTimestamp = $now->getTimestamp();
        $query = $this->entityManager->createQuery("delete from \Concrete\Core\Entity\Command\Process p where p.dateCompleted < :minTimestamp and p.dateCompleted is not null");
        $query->setParameter('minTimestamp', $minTimestamp);
        $query->execute();
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
