<?php
namespace Concrete\Core\Console\Command;

use Bernard\Consumer;
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
use Exception;
use PortlandLabs\LibertaServer\Pipeline\Command\Queue\CodeDeploy\PrepareActionCommand;

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
            ->addArgument('queue', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Names of one or more queues that will be consumed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cms = Core::make('app');

        $bus = $cms->make('bus');
        $receiver = new SeparateBusReceiver($bus);
        $router = new ClassNameRouter();
        $router->add(QueueableCommand::class, $receiver);

        $consumer = new Consumer($router, $cms->make('director'));
        $queue = $this->getQueue($input->getArgument('queue'));
        $consumer->consume($queue, $input->getOptions());
    }

    /**
     * @param array|string $queue
     *
     * @return Queue
     */
    protected function getQueue($queue)
    {
        $cms = Core::make('app');
        $queues = new PersistentFactory($cms->make('queue/driver'), $cms->make('queue/serializer'));
        if (count($queue) > 1) {
            $queues = array_map([$queues, 'create'], $queue);

            return new RoundRobinQueue($queues);
        }

        if (is_array($queue)) {
            $queue = $queue[0];
        }

        return $queues->create($queue);
    }

}
