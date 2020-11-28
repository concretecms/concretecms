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

defined('C5_EXECUTE') or die("Access Denied.");

class RescanFilesController extends AbstractController
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
        return t('Rescan Files');
    }

    public function getDescription(): string
    {
        return t('Recomputes all attributes, clears and regenerates all thumbnails for a file.');
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $definition->addField(new Field('after', t('After File ID'), t('Rescan files after a particular file ID')));
        return $definition;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $query = $this->db->createQueryBuilder();
        $query->select('fID')->from('Files', 'f');
        if ($input->hasField('after')) {
            $after = $input->getField('after');
            $query->andWhere('f.fID > :after');
            $query->setParameter('after', $after->getValue());
        }

        $query->orderBy('f.fID', 'asc');

        $batch = Batch::create();
        foreach($query->execute()->fetchAll() as $result) {
            $batch->add(new RescanFileTaskCommand($result['fID']));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('File rescan beginning...'));
    }
}
