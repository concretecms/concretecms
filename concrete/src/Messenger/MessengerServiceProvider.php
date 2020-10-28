<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\Registry\RegistryInterface;
use Concrete\Core\Messenger\Transport\Sender\CoreDoctrineSendersLocator;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MessengerServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->app->singleton(SerializerInterface::class, Serializer::class);

        $buses = (array) $this->app->make('config')->get('concrete.messenger.buses');
        $this->app->singleton(MessageBusManager::class, function(Application $app) use ($buses) {
            $manager = new MessageBusManager();
            foreach ($buses as $handle => $registryClass) {
                /**
                 * @var $registry RegistryInterface
                 */
                $registry = $app->make($registryClass);
                $manager->addBus($handle, $registry->getBusBuilder($handle));
            }
            return $manager;
        });

        $this->app->singleton(MessageBusInterface::class, function(Application $app) {
            return $app->make(MessageBusManager::class)->getBus(MessageBusManager::BUS_DEFAULT);
        });
    }
}