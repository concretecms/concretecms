<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\BernardEvents;
use Bernard\Event\EnvelopeEvent;
use Bernard\Event\RejectEnvelopeEvent;
use Concrete\Core\System\Mutex\MutexInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BernardSubscriber implements EventSubscriberInterface
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
        return array(
            BernardEvents::REJECT => array(
                array('onReject')
            )
        );
    }

    public function onReject(RejectEnvelopeEvent $event)
    {
        $this->logger->error(t('Error processing queue item: %s – %s',
            $event->getEnvelope()->getMessage()->getName(),
            $event->getException()->getMessage()
        ));
    }
}