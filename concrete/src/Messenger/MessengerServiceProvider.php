<?php
namespace Concrete\Core\Messenger;

use Concrete\Controller\Backend\Messenger;
use Concrete\Core\Application\Application;
use Concrete\Core\Command\Process\Command\HandleProcessMessageCommandHandler;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Command\Batch\Command\HandleBatchMessageCommandHandler;
use Concrete\Core\Messenger\Bus\BusInterface;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Concrete\Core\Messenger\Transport\TransportManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use Concrete\Core\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class MessengerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(SerializerInterface::class, Serializer::class);


        $this->app
            ->when(Serializer::class)
            ->needs(SymfonySerializerInterface::class)
            ->give(function(Application $app) {
                $encoders = [new XmlEncoder(), new JsonEncoder()];
                $normalizers = [new CustomNormalizer(), new ArrayDenormalizer(), new ObjectNormalizer()];
                $serializer = new SymfonySerializer($normalizers, $encoders);
                return $serializer;
            });

        $config = $this->app->make('config');

        $this->app->singleton(
            MessageBusManager::class,
            function (Application $app) use ($config) {
                $manager = new MessageBusManager($app, $config);
                return $manager;
            }
        );

        $this->app->singleton(
            TransportManager::class,
            function (Application $app) use ($config) {
                $manager = new TransportManager();
                foreach ((array) $config->get('concrete.messenger.transports') as $transportClass) {
                    /**
                     * @var $transport TransportInterface
                     */
                    $transport = $app->make($transportClass);
                    $manager->addTransport($transport);
                }
                return $manager;
            }
        );

        $routing = (array) $config->get('concrete.messenger.routing');
        $routing['Concrete\Core\Command\Batch\Command\HandleBatchMessageCommand'] = ['async'];
        $routing['Concrete\Core\Command\Process\Command\HandleProcessMessageCommand'] = ['async'];

        $this->app
            ->when(SendersLocator::class)
            ->needs('$sendersMap')
            ->give($routing);
        $this->app
            ->when(SendersLocator::class)
            ->needs(ContainerInterface::class)
            ->give(function(Application $app) {
                return $app->make(TransportManager::class)->getSenders();
            });

        $this->app->singleton(
            MessageBusInterface::class,
            function (Application $app) use ($config) {
                return $app->make(MessageBusManager::class)->getBus(
                    $config->get('concrete.messenger.default_bus')
                );
            }
        );

        $this->app->when(ConsumeMessagesCommand::class)
            ->needs(ContainerInterface::class)
            ->give(
                function (Application $app) {
                    return $app->make(TransportManager::class)->getReceivers();
                }
            );

        $this->app
            ->when(RoutableMessageBus::class)
            ->needs(ContainerInterface::class)
            ->give(MessageBusManager::class);

        $this->app
            ->when(ConsumeMessagesCommand::class)
            ->needs(LoggerInterface::class)
            ->give(function () {
                $factory = $this->app->make(LoggerFactory::class);
                return $factory->createLogger(Channels::CHANNEL_MESSENGER);
            });
        $this->app
            ->when(Messenger::class)
            ->needs(LoggerInterface::class)
            ->give(function () {
                $factory = $this->app->make(LoggerFactory::class);
                return $factory->createLogger(Channels::CHANNEL_MESSENGER);
            });

        $this->app
            ->when(MessengerEventSubscriber::class)
            ->needs(LoggerInterface::class)
            ->give(function () {
                $factory = $this->app->make(LoggerFactory::class);
                return $factory->createLogger(Channels::CHANNEL_MESSENGER);
            });

        /**
         * @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcher
         */
        $dispatcher = $this->app->make(EventDispatcher::class)->getEventDispatcher();
        $dispatcher->addSubscriber($this->app->make(MessengerEventSubscriber::class));


    }
}