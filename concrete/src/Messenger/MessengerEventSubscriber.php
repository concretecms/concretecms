<?php
namespace Concrete\Core\Messenger;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

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
            WorkerMessageFailedEvent::class => 'handleWorkerMessageFailedEvent',
        ];
    }

    public function handleWorkerMessageFailedEvent(WorkerMessageFailedEvent $event)
    {
        $exception = $event->getThrowable();
        $this->logger->alert(
            sprintf(
                "Messenger Worker Message Failed: %s:%d %s\n",
                $exception->getFile(),
                $exception->getLine(),
                $exception->getMessage()
            ),
            [$exception]
        );
    }

}