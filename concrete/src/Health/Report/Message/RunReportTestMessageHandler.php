<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Command\Batch\BatchAwareInterface;
use Concrete\Core\Command\Batch\BatchAwareTrait;
use Concrete\Core\Health\Report\RunnerFactory;

class RunReportTestMessageHandler implements BatchAwareInterface
{

    use BatchAwareTrait;

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
        $batch = $this->getBatch();
        if (!$batch) {
            throw new \RuntimeException(t('Unable to determine batch for RunReportTestMessageHandler'));
        }
        $runner = $this->runnerFactory->createOrGetRunner($batch);
        $test = $message->getTest();
        $test->run($runner);
    }
    
}