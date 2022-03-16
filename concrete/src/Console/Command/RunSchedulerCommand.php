<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Command\Task\Command\ExecuteConsoleTaskCommand;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Console\Command;
use Concrete\Core\Entity\Command\ScheduledTask;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\EntityManager;

class RunSchedulerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('concrete:scheduler:run')
            ->setDescription('Runs the task scheduler, dispatching any tasks whose time has come.');
        ;
    }

    public function handle(Repository $config, EntityManager $em)
    {
        $timezone = new \DateTimeZone($config->get('app.server_timezone'));
        $now = new \DateTime('now', $timezone);
        $schedules = $em->getRepository(ScheduledTask::class)->findAll();
        $app = Facade::getFacadeApplication();
        foreach ($schedules as $scheduledTask) {
           if ($scheduledTask->getCronExpressionObject()->isDue($now->format('Y-m-d H:i:s'))) {
               // Execute the task since it's the right time.
               $input = $scheduledTask->getTaskInput();
               $task = $scheduledTask->getTask();

               $command = new ExecuteConsoleTaskCommand($task, $input, $this->output);
               $app->executeCommand($command);
           }
        }
        return static::SUCCESS;
    }
}
