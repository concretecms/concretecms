<?php

namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Core;

class ClearCacheCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:clear-cache')
            ->setDescription('Clear the concrete5 cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Clearing the concrete5 cache... ');
        $cms = Core::make('app');
        $cms->clearCaches();
        $output->writeln('done.');
    }
}
