<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Command\Batch\BatchUpdater;
use Concrete\Core\Command\Batch\Command\BatchProcessMessageInterface;
use Concrete\Core\Command\Process\Command\ProcessMessageInterface;
use Concrete\Core\Command\Process\ProcessUpdater;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class MessengerEventSubscriber implements EventSubscriberInterface
{

    /**
     * @var BatchUpdater
     */
    protected $batchUpdater;

    /**
     * @var ProcessUpdater
     */
    protected $processUpdater;

    public function __construct(BatchUpdater $batchUpdater, ProcessUpdater $processUpdater)
    {
        $this->batchUpdater = $batchUpdater;
        $this->processUpdater = $processUpdater;
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageHandledEvent::class => 'handleWorkerMessageHandledEvent',
            WorkerMessageFailedEvent::class => 'handleWorkerMessageFailedEvent',
        ];
    }

    public function handleWorkerMessageHandledEvent(WorkerMessageHandledEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if ($message instanceof BatchProcessMessageInterface) {
            $this->batchUpdater->updateJobs($message->getBatch(), BatchUpdater::COLUMN_PENDING, -1);
            $this->batchUpdater->checkBatchProcessForClose($message->getBatch());
        } else if ($message instanceof ProcessMessageInterface) {
            $this->processUpdater->closeProcess($message->getProcess(), ProcessMessageInterface::EXIT_CODE_SUCCESS);
        }
    }

    public function handleWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if ($message instanceof BatchProcessMessageInterface) {
            $this->batchUpdater->updateJobs($message->getBatch(), BatchUpdater::COLUMN_PENDING, -1);
            $this->batchUpdater->updateJobs($message->getBatch(), BatchUpdater::COLUMN_FAILED, 1);
            $this->batchUpdater->checkBatchProcessForClose($message->getBatch());
        } else if ($message instanceof ProcessMessageInterface) {
            $this->processUpdater->closeProcess(
                $message->getProcess(),
                ProcessMessageInterface::EXIT_CODE_FAILURE,
                $event->getThrowable()->getMessage()
            );
        }
    }

}