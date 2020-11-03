<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Messenger\Registry\RegistryInterface;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Concrete\Core\Messenger\Transport\TransportManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MessengerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(SerializerInterface::class, Serializer::class);

        $config = $this->app->make('config');

        $this->app->singleton(
            MessageBusManager::class,
            function (Application $app) use ($config) {
                $manager = new MessageBusManager();
                foreach ((array) $config->get('concrete.messenger.buses') as $handle => $registryClass) {
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

        $this->app
            ->when(SendersLocator::class)
            ->needs('$sendersMap')
            ->give((array) $config->get('concrete.messenger.routing'));
        $this->app
            ->when(SendersLocator::class)
            ->needs(ContainerInterface::class)
            ->give(function(Application $app) {
                return $app->make(TransportManager::class)->getSenders();
            });

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
                    return $app->make(TransportManager::class)->getReceivers();
                }
            );

        $this->app
            ->when(RoutableMessageBus::class)
            ->needs('$fallbackBus')
            ->give('messenger/bus/command');

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

    }
}