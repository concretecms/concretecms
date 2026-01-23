<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\Command\RescanFileCommand;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RescanFilesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:rescan-files')
            ->setDescription('Rescans all files in the file manager.')
            ->addOption('after', 'a', InputOption::VALUE_REQUIRED, 'Rescan files after a particular file ID.')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of files to scan in this batch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Rescanning files... ');
        $app = Facade::getFacadeApplication();
        $db = $app->make(Connection::class);
        /**
         * @var Connection $db
         */
        $query = $db->createQueryBuilder();
        $query->select('fID')->from('Files', 'f');
        if ($input->getOption('after') !== null) {
            $query->andWhere('f.fID > :after');
            $query->setParameter('after', $input->getOption('after'));
        }
        $query->orderBy('f.fID', 'asc');
        if ($input->getOption('limit') !== null) {
            $query->setMaxResults($input->getOption('limit'));
        }
        $count = 0;
        foreach ($query->execute()->fetchAll() as $result) {
            $command = new RescanFileCommand($result['fID']);
            $output->writeln(t('Rescanning file ID: %s', $result['fID']));
            $app->executeCommand($command);
            $count++;
        }
        $output->writeln("{$count} files rescanned.");

        return static::SUCCESS;
    }
}
