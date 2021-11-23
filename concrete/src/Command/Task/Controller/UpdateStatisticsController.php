<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\Field;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Command\RemoveOldPageVersionsTaskCommand;
use Concrete\Core\Page\Command\UpdateStatisticsTrackersTaskCommand;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class UpdateStatisticsController extends AbstractController
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
        return t('Update Statistics Trackers');
    }

    public function getDescription(): string
    {
        return t('Scan the sitemap for file usage and stack usage to update statistics trackers.');
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $definition->addField(new Field('after', t('After Page ID'), t('Updates trackers on pages after a particular page ID.')));
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

        $query->orderBy('p.cID', 'asc');

        $batch = Batch::create();
        foreach($query->execute()->fetchAll() as $result) {
            $batch->add(new UpdateStatisticsTrackersTaskCommand($result['cID']));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Updating statistics trackers...'));
    }


}
