<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Messenger\Batch\BatchProcessUpdater;
use Concrete\Core\Messenger\Batch\Command\BatchProcessMessageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class MessengerEventSubscriber implements EventSubscriberInterface
{

    /**
     * @var BatchProcessUpdater
     */
    protected $batchProcessUpdater;

    public function __construct(BatchProcessUpdater $batchProcessUpdater)
    {
        $this->batchProcessUpdater = $batchProcessUpdater;
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
            $this->batchProcessUpdater->updateJobs($message->getBatchProcess(), BatchProcessUpdater::COLUMN_PENDING, -1);
        }
    }

    public function handleWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if ($message instanceof BatchProcessMessageInterface) {
            $this->batchProcessUpdater->updateJobs($message->getBatchProcess(), BatchProcessUpdater::COLUMN_PENDING, -1);
            $this->batchProcessUpdater->updateJobs($message->getBatchProcess(), BatchProcessUpdater::COLUMN_FAILED, 1);
        }
    }

}