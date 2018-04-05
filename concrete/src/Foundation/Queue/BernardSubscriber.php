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

    /**
     * @var MutexInterface
     */
    protected $mutex;

    /**
     * @var QueueMutexKeyGenerator
     */
    protected $keyGenerator;

    public function __construct(QueueMutexKeyGenerator $keyGenerator, LoggerInterface $logger, MutexInterface $mutex)
    {
        $this->keyGenerator = $keyGenerator;
        $this->logger = $logger;
        $this->mutex = $mutex;
    }

    public static function getSubscribedEvents()
    {
        return array(
            BernardEvents::INVOKE => array(
                array('onInvoke')
            ),

            BernardEvents::ACKNOWLEDGE => array(
                array('onAcknowledge')
            ),

            BernardEvents::REJECT => array(
                array('onReject')
            )
        );
    }

    public function onInvoke(EnvelopeEvent $event)
    {
        $this->mutex->acquire($this->keyGenerator->getMutexKey($event->getQueue()));
    }

    public function onAcknowledge(EnvelopeEvent $event)
    {
        $this->mutex->release($this->keyGenerator->getMutexKey($event->getQueue()));
    }

    public function onReject(RejectEnvelopeEvent $event)
    {
        $this->mutex->release($this->keyGenerator->getMutexKey($event->getQueue()));
        $this->logger->error(t('Error processing queue item: %s – %s',
            $event->getEnvelope()->getMessage()->getName(),
            $event->getException()->getMessage()
        ));
    }
}