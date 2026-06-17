<?php

namespace Concrete\Core\Command\Task\Runner\Context;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Messenger\Stamp\SkipSendersStamp;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ConsoleContext extends AbstractContext
{

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(
        Application $app,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $messageBus,
        OutputInterface $output
    ) {
        $this->app = $app;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($messageBus, $output);
    }

    public function dispatchCommand($command, ?array $stamps = null): void
    {
        $newStamps = [
            new SkipSendersStamp(),
            new OutputStamp($this->getOutput())
        ];
        if (!is_null($stamps)) {
            $stamps = array_merge($newStamps, $stamps);
        } else {
            $stamps = $newStamps;
        }
        try {
            $this->messageBus->dispatch($command, $stamps);
        } catch (HandlerFailedException $e) {
            $envelope = Envelope::wrap($command, $stamps);
            $failedEvent = new WorkerMessageFailedEvent($envelope, TransportInterface::DEFAULT_SYNC, $e);
            $this->eventDispatcher->dispatch($failedEvent);
        }
    }


}
