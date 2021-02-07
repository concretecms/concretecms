<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\Field;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\Command\RescanFileCommand;
use Concrete\Core\File\Command\RescanFileTaskCommand;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;
use Concrete\Core\User\Command\CheckAutomatedGroupsCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class CheckAutomatedGroupsController extends AbstractController
{

    /**
     * @var Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getName(): string
    {
        return t('Check Automated Groups');
    }

    public function getDescription(): string
    {
        return t('Automatically add users to groups.');
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $definition->addField(new Field('after', t('After User ID'), t('Starts the process after a particular user ID.')));
        return $definition;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $query = $this->db->createQueryBuilder();
        $query->select('uID')->from('Users', 'u');
        if ($input->hasField('after')) {
            $after = $input->getField('after');
            $query->andWhere('u.uID > :after');
            $query->setParameter('after', $after->getValue());
        }

        $query->orderBy('u.uID', 'asc');

        $batch = Batch::create();
        foreach($query->execute()->fetchAll() as $result) {
            $batch->add(new CheckAutomatedGroupsCommand($result['uID']));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Checking automated groups for users...'));
    }
}
