<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Health\Report\RunnerFactory;

class RunReportTestMessageHandler
{

    /**
     * @var RunnerFactory
     */
    protected $runnerFactory;

    public function __construct(RunnerFactory $runnerFactory)
    {
        $this->runnerFactory = $runnerFactory;
    }

    public function __invoke(RunReportTestMessage $message)
    {
        $runner = $this->runnerFactory->createRunnerFromResultId($message->getResultId());
        $test = $message->getTest();
        $test->run($runner);
    }
    
}