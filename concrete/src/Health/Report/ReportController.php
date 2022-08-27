<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Health\Report\Message\FinishReportMessage;
use Concrete\Core\Health\Report\Message\GradeReportMessage;
use Concrete\Core\Health\Report\Message\RunReportTestMessage;

abstract class ReportController extends AbstractController implements ReportControllerInterface
{

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    public function __construct(ResultFactory $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $result = $this->resultFactory->createResult($task);
        $suite = $this->getTestSuite();
        $batch = Batch::create();
        foreach($suite->getTests() as $test) {
            $batch->add(new RunReportTestMessage($result->getId(), $test));
        }
        $batch->add(new GradeReportMessage($result->getId()));
        $batch->add(new FinishReportMessage($result->getId()));
        return new BatchProcessTaskRunner($task, $batch, $input, t('Generating report...'));
    }


}
