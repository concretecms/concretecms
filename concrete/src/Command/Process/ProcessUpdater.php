<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Process\Event\ProcessEvent;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Command\Batch as BatchEntity;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Entity\Command\TaskProcess;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Notification\Events\MercureService;
use Concrete\Core\Notification\Events\ServerEvent\ProcessClosedEvent;
use Concrete\Core\Notification\Mercure\Update\BatchUpdated;
use Concrete\Core\Notification\Events\ServerEvent\ProcessClosed;
use Doctrine\ORM\EntityManager;

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
     * @var MercureService
     */
    protected $mercureService;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(
        EntityManager $entityManager,
        EventDispatcher $eventDispatcher,
        Date $dateService,
        Repository $config,
        MercureService $mercureService
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->dateService = $dateService;
        $this->config = $config;
        $this->mercureService = $mercureService;
    }

    /**
     * @param string|Process $process
     */
    public function closeProcess($process, int $exitCode, string $exitMessage = null)
    {
        if (is_string($process)) {
            $process = $this->entityManager->find(Process::class, $process);
        }
        $dateCompleted = $this->dateService->toDateTime()->getTimestamp();
        $process->setDateCompleted($dateCompleted);
        $process->setExitCode($exitCode);
        $process->setExitMessage($exitMessage);
        $this->entityManager->persist($process);
        $this->entityManager->flush();

        if ($this->mercureService->isEnabled()) {
            usleep(500000); // some fast-running tasks cause race conditions.
            $event = new ProcessClosedEvent($process->jsonSerialize(), $exitCode);
            $hub = $this->mercureService->getHub();
            $hub->publish($event->getUpdate());
        }

        $this->clearOldProcesses();
        if ($process instanceof TaskProcess) {
            $task = $process->getTask();
            $task->setDateLastCompleted($dateCompleted);
            $this->entityManager->persist($process);
            $this->entityManager->flush();
        }

        $this->eventDispatcher->dispatch('on_close_process', new ProcessEvent($process));
    }

    protected function clearOldProcesses()
    {
        $threshold = (int) $this->config->get('concrete.processes.delete_threshold');
        $now = new \DateTime();
        $now->sub(new \DateInterval('P' . $threshold . 'D'));
        $minTimestamp = $now->getTimestamp();
        $query = $this->entityManager->createQuery(
            "select p from \Concrete\Core\Entity\Command\Process p where p.dateCompleted < :minTimestamp and p.dateCompleted is not null"
        );
        $query->setParameter('minTimestamp', $minTimestamp);
        $processes = $query->getResult();
        if ($processes) {
            foreach ($processes as $process) {
                $this->entityManager->remove($process);
            }
        }
        $this->entityManager->flush();
    }

}
