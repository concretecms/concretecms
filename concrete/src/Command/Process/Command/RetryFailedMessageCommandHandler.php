<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Messenger\Transport\FailedTransportManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\Receiver\SingleMessageReceiver;
use Symfony\Component\Messenger\Worker;

class RetryFailedMessageCommandHandler extends AbstractFailedMessageCommandHandler implements LoggerAwareInterface
{

    use LoggerAwareTrait;


    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(FailedTransportManager $failedTransportManager, MessageBusInterface $messageBus, EventDispatcherInterface $eventDispatcher)
    {
        $this->messageBus = $messageBus;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($failedTransportManager);
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_MESSENGER;
    }

    public function __invoke(RetryFailedMessageCommand $command)
    {
        $receiver = $this->getReceiverFromCommand($command);
        $envelope = $receiver->find($command->getMessageId());
        $singleReceiver = new SingleMessageReceiver($receiver, $envelope);

        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(1));

        $worker = new Worker(
            [$command->getReceiverName() => $singleReceiver],
            $this->messageBus,
            $this->eventDispatcher,
            $this->logger
        );

        $worker->run();

        $count = -1;
        if ($receiver instanceof MessageCountAwareInterface) {
            $count = $receiver->getMessageCount();
        }
        return $count;
    }
}