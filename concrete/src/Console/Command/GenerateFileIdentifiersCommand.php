<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFileIdentifiersCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:files:generate-identifiers')
            ->setDescription('Create unique identifiers for existing files.')
            ->addOption('reset', 'a', InputOption::VALUE_NONE,
                "Reset all generated unique identifiers.")
            ->setHelp(<<<EOT
This command will create unique identifiers for existing files. This is required for enabling
the secure download feature.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doReset = $this->input->getOption('reset') !== false;

        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $fileList = new FileList();
        $fileList->ignorePermissions();

        foreach ($fileList->getResults() as $result) {
            if ($result instanceof File) {
                if ($doReset) {
                    $result->resetFileUUID();
                } else {
                    $result->generateFileUUID();
                }

                $entityManager->persist($result);
            }
        }

        $entityManager->flush();

        $output->writeln(sprintf("Unique identifier has been successfully changed for %s files.", $fileList->getTotalResults()));

        return static::SUCCESS;
    }
}
