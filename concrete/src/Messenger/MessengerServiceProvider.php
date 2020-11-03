<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Messenger\Receiver\ReceiverLocator;
use Concrete\Core\Messenger\Registry\RegistryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MessengerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(SerializerInterface::class, Serializer::class);

        $buses = (array) $this->app->make('config')->get('concrete.messenger.buses');

        $this->app->singleton(
            MessageBusManager::class,
            function (Application $app) use ($buses) {
                $manager = new MessageBusManager();
                foreach ($buses as $handle => $registryClass) {
                    /**
                     * @var $registry RegistryInterface
                     */
                    $registry = $app->make($registryClass);
                    $manager->addBus($handle, $registry->getBusBuilder($handle));
                }
                return $manager;
            }
        );

        $this->app->singleton(
            ReceiverLocator::class,
            function (Application $app) use ($buses) {
                $receiverLocator = new ReceiverLocator();
                foreach ($buses as $handle => $registryClass) {
                    /**
                     * @var $registry RegistryInterface
                     */
                    $registry = $app->make($registryClass);
                    $receivers = $registry->getReceivers();
                    foreach($receivers as $handle => $receiver) {
                        $receiverLocator->addReceiver($handle, $receiver);
                    }
                }
                return $receiverLocator;
            }
        );

        $this->app->singleton(
            MessageBusInterface::class,
            function (Application $app) {
                return $app->make(MessageBusManager::class)->getBus(MessageBusManager::BUS_DEFAULT);
            }
        );

        $this->app->when(ConsumeMessagesCommand::class)
            ->needs(ContainerInterface::class)
            ->give(
                function (Application $app) {
                    return $app->make(ReceiverLocator::class);
                }
            );

        $this->app->when(RoutableMessageBus::class)->needs('$fallbackBus')->give('messenger/bus/command');
        $this->app->when(RoutableMessageBus::class)->needs(ContainerInterface::class)->give(MessageBusManager::class);

        $this->app
            ->when(ConsumeMessagesCommand::class)
            ->needs(LoggerInterface::class)
            ->give(function () {
                $factory = $this->app->make(LoggerFactory::class);
                return $factory->createLogger(Channels::CHANNEL_MESSENGER);
            });

    }
}