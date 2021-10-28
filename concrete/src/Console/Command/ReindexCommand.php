<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Console\Command;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Express\Search\Index\EntityIndex;
use Concrete\Core\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\DefaultManager;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Search\Index\IndexObjectProvider;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReindexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:reindex')
            ->setDescription('Reindex pages, files, users and express entities')
            ->addOption('pages', 'p', InputOption::VALUE_NONE, 'Include pages in the reindex')
            ->addOption('express', 'e', InputOption::VALUE_NONE, 'Include express in the reindex')
            ->addOption('since', null, InputOption::VALUE_REQUIRED, 'Start at a certain ID')
            ->setCanRunAsRoot(false);
    }

    protected function clearCache(IndexManagerInterface $indexManager, $type, $message)
    {
        $indexManager->clear($type);
        $this->output->writeln($message);
    }

    protected function reindex(IndexManagerInterface $indexManager, $type, $id, $message)
    {
        $indexManager->index($type, $id);
        $this->output->writeln($message);
    }

    public function handle(
        Application $app,
        IndexManagerInterface $indexManager,
        IndexObjectProvider $dataProvider,
        ObjectManager $objectManager,
        EntityManager $entityManager)
    {
        $includePages = $this->input->getOption('pages');
        $includeExpress = $this->input->getOption('express');
        $since = (int) $this->input->getOption('since');
        if ($includePages || $includeExpress) {

            if ($includePages && $since === 0) {
                $this->clearCache($indexManager, Page::class, t('Clearing page index...'));
            }

            if ($includeExpress && $since === 0) {
                foreach ($dataProvider->fetchExpressObjects() as $id) {
                    $object = $objectManager->getObjectByID($id);
                    if ($object) {
                        $entityIndexManager = $app->make(EntityIndex::class, ['entity' => $object]);
                        $entityIndexManager->clear();
                        $this->output->writeln(t('Clearing index for express object: %s (%s)',
                            $object->getName(),
                            $object->getID()
                        ));
                    }
                }
            }

            if ($includePages) {
                $batchCount = 0;
                foreach ($dataProvider->fetchPages() as $id) {
                    if ($id >= $since) {
                        $this->reindex($indexManager, Page::class, $id, t('Reindexing page ID %s', $id));
                        $batchCount++;
                        if ($batchCount == 500) {
                            $entityManager->clear();
                            $batchCount = 0;
                        }
                    }
                }
            }

            if ($includeExpress) {
                $batchCount = 0;
                foreach ($dataProvider->fetchExpressEntries() as $id) {
                    if ($id >= $since) {
                        $this->reindex($indexManager, Entry::class, $id, t('Reindexing express entry ID %s', $id));
                        $batchCount++;
                        if ($batchCount == 100) {
                            $entityManager->clear();
                            $batchCount = 0;
                        }
                    }
                }
            }

            return static::SUCCESS;
        } else {
            throw new \Exception(t('You must include at least one type to reindex. Valid types are --pages/-p, --express/-e'));
        }

    }
}
