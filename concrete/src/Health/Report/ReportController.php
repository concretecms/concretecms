<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\Field;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Health\Report\Message\EmailReportMessage;
use Concrete\Core\Health\Report\Message\FinishReportMessage;
use Concrete\Core\Health\Report\Message\GradeReportMessage;
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

    public function getConsoleCommandName(): string
    {
        return 'health:' . parent::getConsoleCommandName();
    }

    protected function getResult(TaskInterface $task, InputInterface $input): Result
    {
        return new Result();
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $definition->addField(new Field('email', t('Email'), t('Email address to send the completed report to.')));
        return $definition;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $email = null;
        if ($input->hasField('email')) {
            $email = $input->getField('email')->getValue();
        }

        $result = $this->getResult($task, $input);
        $result->setName($this->getName());
        $result->setDateStarted(time());
        $result->setTask($task);

        $this->entityManager->persist($result);
        $this->entityManager->flush();

        $suite = $this->getTestSuite();
        $batch = Batch::create();
        foreach($suite->getTests() as $test) {
            $batch->add(new RunReportTestMessage($result->getId(), $test));
        }
        $batch->add(new GradeReportMessage($result->getId()));

        if ($email) {
            $batch->add(new EmailReportMessage($email, $result->getId()));
        }

        $batch->add(new FinishReportMessage($result->getId()));

        return new BatchProcessTaskRunner($task, $batch, $input, t('Generating report...'));
    }


}
