<?php
namespace Concrete\Core\Console\Command;

use Bernard\BernardEvents;
use Bernard\Consumer;
use Bernard\Event\EnvelopeEvent;
use Bernard\Queue\RoundRobinQueue;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Router\ClassNameRouter;
use Bernard\Router\SimpleRouter;
use Bernard\Serializer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Console\Command;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\Receiver\SameBusReceiver;
use League\Tactician\Bernard\Receiver\SeparateBusReceiver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Core;
use Concrete\Core\Foundation\Queue\QueueService;

class QueueProcessCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('queue:process')
            ->addOption('max-runtime', null, InputOption::VALUE_OPTIONAL, 'Maximum time in seconds the consumer will run.', null)
            ->addOption('max-messages', null, InputOption::VALUE_OPTIONAL, 'Maximum number of messages that should be consumed.', null)
            ->addOption('stop-when-empty', null, InputOption::VALUE_NONE, 'Stop consumer when queue is empty.', null)
            ->addOption('stop-on-error', null, InputOption::VALUE_NONE, 'Stop consumer when an error occurs.', null)
            ->addArgument('queue', InputOption::VALUE_OPTIONAL, 'Names of one or more queues that will be consumed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Core::make('app');
        /*
        $eventDispatcher = $app->make('director');
        $eventDispatcher->addListener(
            BernardEvents::INVOKE,
            function(EnvelopeEvent $envelopeEvent) {
                echo PHP_EOL . 'Processing: ' . $envelopeEvent->getEnvelope()->getClass();
            }
        );
        $eventDispatcher->addListener(
            BernardEvents::ACKNOWLEDGE,
            function(EnvelopeEvent $envelopeEvent) {
                echo PHP_EOL . 'Processed: ' . $envelopeEvent->getEnvelope()->getClass();
            }
        );
        $eventDispatcher->addListener(
            BernardEvents::REJECT,
            function(EnvelopeEvent $envelopeEvent) {
                echo PHP_EOL . 'Failed: ' . $envelopeEvent->getEnvelope()->getClass();
                // you can also log error messages here
            }
        );
        */

        $queueName = $input->getArgument('queue');

        $consumer = $app->make('queue/consumer');
        $queue = $app->make(QueueService::class)->get($queueName);
        $consumer->consume($queue, $input->getOptions());
    }

}
