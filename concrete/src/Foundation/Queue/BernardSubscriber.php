<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\BernardEvents;
use Bernard\Event\EnvelopeEvent;
use Bernard\Event\RejectEnvelopeEvent;
use Concrete\Core\Foundation\Queue\Batch\BatchProgressUpdater;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Concrete\Core\System\Mutex\MutexInterface;
use League\Tactician\Bernard\QueueCommand;
use League\Tactician\Bernard\QueuedCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BernardSubscriber implements EventSubscriberInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var BatchProgressUpdater
     */
    protected $updater;

    public function __construct(LoggerInterface $logger, BatchProgressUpdater $updater)
    {
        $this->logger = $logger;
        $this->updater = $updater;
    }

    public static function getSubscribedEvents()
    {
        return array(
            BernardEvents::REJECT => array(
                array('onReject')
            )
        );
    }

    public function onReject(RejectEnvelopeEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        $this->logger->error(t('Error processing queue item: %s – %s',
            $message->getName(),
            $event->getException()->getMessage()
        ));

        $command = $message; // the command itself might be our queued command.
        if ($message instanceof QueueCommand) {
            // we have wrapped a different comment
            $command = $message->getCommand();
        }

        if ($command instanceof BatchableCommandInterface) {
            $this->updater->incrementCommandProgress($command);
        }


    }
}