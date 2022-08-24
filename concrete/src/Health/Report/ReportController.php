<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Health\Report\Message\RunReportTestMessage;
use Doctrine\ORM\EntityManager;

abstract class ReportController extends AbstractController implements ReportControllerInterface
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $suite = $this->getTestSuite();
        $batch = Batch::create();
        foreach($suite->getTests() as $test) {
            $batch->add(new RunReportTestMessage($test));
        }
        return new BatchProcessTaskRunner($task, $batch, $input, t('Generating report...'));
    }


}
