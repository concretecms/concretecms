<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\Definition\BooleanField;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\Field;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Command\RemoveOldPageVersionsTaskCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class RemoveOldPageVersionsController extends AbstractController
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
        return t('Remove Old Page Versions');
    }

    public function getDescription(): string
    {
        return t('Removes all except the 10 most recent page versions for each page.');
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $definition->addField(new Field('after', t('After Page ID'), t('Scan pages for versions to remove after a particular page ID.')));
        $definition->addField(new BooleanField('exclude_system_pages', t('Exclude System Pages (Including Trash)'), t('Skip system pages, including dashboard pages, stacks, drafts, and pages in the trash.'), true));
        return $definition;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $query = $this->db->createQueryBuilder();
        $query->select('cID')->from('Pages', 'p');
        if ($input->hasField('after')) {
            $after = $input->getField('after');
            $query->andWhere('p.cID > :after');
            $query->setParameter('after', $after->getValue());
        }
        if ($input->hasField('exclude_system_pages') && $input->getField('exclude_system_pages')->getValue()) {
            $query->andWhere('p.cIsSystemPage = 0');
        }

        $query->orderBy('p.cID', 'asc');

        $batch = Batch::create();
        $result = $query->execute();
        foreach ($result->iterateColumn() as $cID) {
            $batch->add(new RemoveOldPageVersionsTaskCommand((int) $cID));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Page version removal beginning...'));
    }



}
