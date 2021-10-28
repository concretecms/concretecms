<?php

namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\Definition\BooleanField;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\Field;
use Concrete\Core\Command\Task\Input\Definition\SelectField;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Express\Command\ClearEntityIndexCommand;
use Concrete\Core\Express\Command\RebuildEntityIndexCommand;
use Concrete\Core\Express\Command\ReindexEntryTaskCommand;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\File\Command\ClearFileIndexCommand;
use Concrete\Core\File\Command\RebuildFileIndexCommand;
use Concrete\Core\User\Command\RebuildUserIndexCommand;
use Concrete\Core\File\Command\ReindexFileTaskCommand;
use Concrete\Core\Page\Command\ClearPageIndexCommand;
use Concrete\Core\Page\Command\RebuildPageIndexCommand;
use Concrete\Core\Page\Command\ReindexPageTaskCommand;
use Concrete\Core\User\Command\ClearUserIndexCommand;
use Concrete\Core\User\Command\ReindexUserTaskCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class ReindexContentController extends AbstractController
{

    use ApplicationAwareTrait;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * ReindexContentController constructor.
     * @param Connection $db
     */
    public function __construct(Connection $db, ObjectManager $objectManager)
    {
        $this->db = $db;
        $this->objectManager = $objectManager;
    }

    public function getName(): string
    {
        return t('Reindex Content');
    }

    public function getDescription(): string
    {
        return t('Reindex pages, files, users and Express objects.');
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $definition->addField(new BooleanField('clear', t('Clear Index'), t('Clear index before reindexing.')));
        $definition->addField(new BooleanField('rebuild', t('Rebuild Index'), t('Rebuild index attributes table by rescanning all keys.')));
        $definition->addField(new Field('after', t('After ID'), t('Reindex objects after a particular ID.')));
        $definition->addField(
            new SelectField(
                'object',
                t('Object to reindex.'),
                t('You must provide what type of object you want to reindex.'),
                [
                    'pages' => t('Pages'),
                    'files' => t('Files'),
                    'users' => t('Users'),
                    'express' => t('Express'),
                ],
                true
            )
        );
        return $definition;
    }


    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $object = (string) $input->getField('object')->getValue();
        $batch = Batch::create();
        $query = $this->db->createQueryBuilder();
        $after = null;

        if ($input->hasField('after')) {
            $after = $input->getField('after')->getValue();
        }

        if ($object == 'pages') {
            if ($input->hasField('clear')) {
                $batch->add(new ClearPageIndexCommand());
            }
            if ($input->hasField('rebuild')) {
                $batch->add(new RebuildPageIndexCommand());
            }
            $query->select('cID')->from('Pages', 'p');
            if ($after) {
                $query->andWhere('p.cID > :after');
                $query->setParameter('after', $after);
            }
            $query->orderBy('p.cID', 'asc');
            foreach($query->execute()->fetchAll() as $result) {
                $batch->add(new ReindexPageTaskCommand($result['cID']));
            }
        } else if ($object == 'files') {
            if ($input->hasField('clear')) {
                $batch->add(new ClearFileIndexCommand());
            }
            if ($input->hasField('rebuild')) {
                $batch->add(new RebuildFileIndexCommand());
            }
            $query->select('fID')->from('Files', 'f');
            if ($after) {
                $query->andWhere('f.fID > :after');
                $query->setParameter('after', $after);
            }
            $query->orderBy('f.fID', 'asc');
            foreach($query->execute()->fetchAll() as $result) {
                $batch->add(new ReindexFileTaskCommand($result['fID']));
            }
        } else if ($object == 'express') {
            if ($input->hasField('clear') || $input->hasField('rebuild')) {
                $entities = $this->objectManager->getEntities(true)->findAll();
                if ($input->hasField('clear')) {
                    foreach($entities as $entity) {
                        $batch->add(new ClearEntityIndexCommand($entity->getId()));
                    }
                }
                if ($input->hasField('rebuild')) {
                    foreach($entities as $entity) {
                        $batch->add(new RebuildEntityIndexCommand($entity->getId()));
                    }
                }
            }

            $query->select('exEntryID')->from('ExpressEntityEntries', 'e');
            if ($after) {
                $query->andWhere('e.exEntryID > :after');
                $query->setParameter('after', $after);
            }
            $query->orderBy('exEntryID', 'asc');
            foreach($query->execute()->fetchAll() as $result) {
                $batch->add(new ReindexEntryTaskCommand($result['exEntryID']));
            }
        } else if ($object == 'users') {
            if ($input->hasField('clear')) {
                $batch->add(new ClearUserIndexCommand());
            }
            if ($input->hasField('rebuild')) {
                $batch->add(new RebuildUserIndexCommand());
            }
            $query->select('uID')->from('Users', 'u');
            if ($after) {
                $query->andWhere('u.uID > :after');
                $query->setParameter('after', $after);
            }
            $query->orderBy('u.uID', 'asc');
            foreach($query->execute()->fetchAll() as $result) {
                $batch->add(new ReindexUserTaskCommand($result['uID']));
            }
        }





        return new BatchProcessTaskRunner($task, $batch, $input, t('Reindexing of %s beginning...', $object));

    }


}
