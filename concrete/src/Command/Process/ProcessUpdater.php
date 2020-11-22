<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Localization\Service\Date;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Command\Batch as BatchEntity;

class ProcessUpdater
{
    /**
     * @var Date
     */
    protected $dateService;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * BatchProgressUpdater constructor.
     * @param Application $app
     */
    public function __construct(EntityManager $entityManager, Date $dateService, Repository $config)
    {
        $this->entityManager = $entityManager;
        $this->dateService = $dateService;
        $this->config = $config;
    }

    /**
     * @param string|Process $process
     */
    public function closeProcess($process, int $exitCode, string $exitMessage = null)
    {
        if (is_string($process)) {
            $process = $this->entityManager->find(Process::class, $process);
        }
        $process->setDateCompleted($this->dateService->toDateTime()->getTimestamp());
        $process->setExitCode($exitCode);
        $process->setExitMessage($exitMessage);
        $this->entityManager->persist($process);
        $this->entityManager->flush();
        $this->clearOldProcesses();
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

}
