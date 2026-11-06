<?php

namespace Concrete\Core\Messenger\Command;

use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand as SymfonyConsumeMessagesCommand;
use Symfony\Component\Messenger\EventListener\ResetServicesListener;
use Symfony\Component\Messenger\RoutableMessageBus;

class ConsumeMessagesCommand extends SymfonyConsumeMessagesCommand
{
    // Symfony reads the default name only from the class that declares it
    protected static $defaultName = 'messenger:consume';

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    public function __construct(RoutableMessageBus $routableBus, ContainerInterface $receiverLocator, EventDispatcherInterface $eventDispatcher, ?LoggerInterface $logger = null, array $receiverNames = [], ?ResetServicesListener $resetServicesListener = null, array $busIds = [])
    {
        $this->logger = $logger;
        parent::__construct($routableBus, $receiverLocator, $eventDispatcher, $logger, $receiverNames, $resetServicesListener, $busIds);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Symfony prints the worker logs (with -vv) only in the full framework, which adds a console handler
        if ($this->logger instanceof Logger) {
            $this->logger->pushHandler(new PsrHandler(new ConsoleLogger($output)));
        }

        return parent::execute($input, $output);
    }
}
