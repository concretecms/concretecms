<?php

namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\TaskInterface;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;

class OutputFactory
{

    public function createDashboardOutput()
    {
        return new DashboardOutput();
    }

    public function createConsoleOutput(ConsoleOutputInterface $output)
    {
        return new ConsoleOutput($output);
    }

}
