<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;

class MessengerEventSubscriber implements EventSubscriberInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerStartedEvent::class => 'handleWorkerStartedEvent',
            WorkerMessageFailedEvent::class => 'handleWorkerMessageFailedEvent',
        ];
    }

    public function handleWorkerStartedEvent(WorkerStartedEvent $event)
    {
        Cache::disableAll(); // If caches aren't disabled on the worker you get some conditions where pages, page versions get stale
    }

    public function handleWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $exception = $event->getThrowable();

        // Log the exception
        $this->logger->alert(
            sprintf(
                "Messenger Worker Message Failed: %s:%d %s\n",
                $exception->getFile(),
                $exception->getLine(),
                $exception->getMessage()
            ),
            [$exception]
        );

        // Is this a task? If so, let's try and output the error in a helpful way
        $envelope = $event->getEnvelope();
        $taskOutputStamp = $envelope->last(OutputStamp::class);
        if ($taskOutputStamp instanceof OutputStamp) {
            $output = $taskOutputStamp->getOutput();
            $output->writeError($exception->getMessage());
        }
    }

}