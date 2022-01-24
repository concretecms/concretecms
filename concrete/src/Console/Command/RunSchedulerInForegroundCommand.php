<?php

namespace Concrete\Core\Console\Command;

use Carbon\Carbon;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Console\Command;
use Concrete\Core\Entity\Command\ScheduledTask;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\ArgvInput;

class RunSchedulerInForegroundCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('concrete:scheduler:run-dev')
            ->setDescription('Runs the task scheduler in the foreground every minute. Useful for development environments.');
        ;
    }

    public function handle()
    {
        $this->output->writeln('Running the worker every minute...');
        $command = $this->getApplication()->find('concrete:scheduler:run');
        while (true) {
            if (Carbon::now()->second === 0) {
                $return = $command->run(new ArgvInput(), $this->output);
                if ($return == static::FAILURE) {
                    return $return;
                }
            }
            sleep(1);
        }
        return static::SUCCESS;
    }
}
