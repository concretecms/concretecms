<?php
namespace Concrete\Core\Console\Command;

use Bernard\BernardEvents;
use Bernard\Event\EnvelopeEvent;
use Bernard\Event\RejectEnvelopeEvent;
use Concrete\Core\Console\Command;
use Concrete\Core\Foundation\Queue\QueueService;
use Core;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueueProcessCommand extends Command
{

    public function getDescription()
    {
        return t('Processes the concrete5 queue. Leaving the queue option blank will process the default queue');
    }

    protected function configure()
    {
        $this
            ->setName('queue:process')
            ->addOption('max-runtime', null, InputOption::VALUE_OPTIONAL, 'Maximum time in seconds the consumer will run.', null)
            ->addOption('max-messages', null, InputOption::VALUE_OPTIONAL, 'Maximum number of messages that should be consumed.', null)
            ->addOption('stop-when-empty', null, InputOption::VALUE_NONE, 'Stop consumer when queue is empty.', null)
            ->addOption('stop-on-error', null, InputOption::VALUE_NONE, 'Stop consumer when an error occurs.', null)
            ->addOption('all', null, InputOption::VALUE_NONE, 'Processes all available queues in a round robin style.', null)
            ->addArgument('queue', InputOption::VALUE_OPTIONAL, 'A single queue or list of queues to be processed. If blank the default queue will be processed.', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Core::make('app');

        if ($output->isVeryVerbose()) {
            $eventDispatcher = $app->make('director');
            $eventDispatcher->addListener(
                BernardEvents::INVOKE,
                function(EnvelopeEvent $envelopeEvent) use ($output) {
                    $output->writeln(t('Processing: %s', $envelopeEvent->getEnvelope()->getClass()));
                }
            );
            $eventDispatcher->addListener(
                BernardEvents::ACKNOWLEDGE,
                function(EnvelopeEvent $envelopeEvent) use ($output) {
                    $output->writeln(t('Processed: %s', $envelopeEvent->getEnvelope()->getClass()));
                }
            );
            $eventDispatcher->addListener(
                BernardEvents::REJECT,
                function(RejectEnvelopeEvent $envelopeEvent) use ($output) {
                    $output->writeln(t('Failed: %s: %s', $envelopeEvent->getEnvelope()->getClass(), $envelopeEvent->getException()->getMessage()));
                }
            );
        }


        $service = $app->make(QueueService::class);
        if ($input->getOption('all') && $input->getArgument('queue')) {
            throw new \Exception(t('You cannot specify a queue and use the --all option'));
        }

        $queueName = $input->getArgument('queue');
        $queue = null;
        if ($queueName) {
            $queue = $queueName[0];
        } else {
            if (!$input->getOption('all')) {
                $queue = $service->getDefaultQueueHandle();
            }
        }

        $options = $input->getOptions();
        if (isset($options['all'])) {
            unset($options['all']); // this comes from our CLI script.
        }

        if (!$output->isQuiet()) {
            if ($queue) {
                $output->writeln(t('Processing queue: %s', $queue));
            } else {
                $output->writeln(t('Processing all queues with round-robin.'));
            }
        }

        $queue = $service->get($queue);
        $service->consume($queue, $options);
    }

}
