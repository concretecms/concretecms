<?php

namespace Concrete\Core\Command\Scheduler;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Command\ScheduledTask;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Cron\CronExpression;
use Doctrine\ORM\EntityManager;

class Scheduler
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

    public function __construct(Date $dateService, Repository $config, EntityManager $entityManager)
    {
        $this->dateService = $dateService;
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function isEnabled(): bool
    {
        return $this->config->get('concrete.processes.scheduler.enable');
    }

    public function createScheduledTask(TaskInterface $task, InputInterface $input, string $cronExpression)
    {
        // Validate cron expression
        try {
            $expressionObject = new CronExpression($cronExpression);
        } catch (\Exception $e) {
            throw new UserMessageException(t('Unable to parse supplied cron expression. Please ensure your cron expression is accurate in a standard format. You may validate your expression at https://crontab.guru'));
        }

        $scheduledTask = new ScheduledTask();
        $scheduledTask->setDateScheduled($this->dateService->toDateTime()->getTimestamp());
        $user = new User();
        if ($user) {
            $userInfo = $user->getUserInfoObject();
            if ($userInfo) {
                $scheduledTask->setUser($userInfo->getEntityObject());
            }
        }
        $scheduledTask->setTask($task);
        $scheduledTask->setInput($input);
        $scheduledTask->setCronExpression($cronExpression);
        $this->entityManager->persist($scheduledTask);
        $this->entityManager->flush();

        return $scheduledTask;
    }


}
