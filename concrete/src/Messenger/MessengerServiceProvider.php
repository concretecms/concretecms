<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Application\Application;
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
        $this->app->singleton(MessageBusManager::class, function(Application $app) {
            $manager = new MessageBusManager();
            $manager->addBus(MessageBusManager::BUS_DEFAULT, function() use ($app) {
                $bus = new MessageBus(
                    [
                        new AddBusNameStampMiddleware(MessageBusManager::BUS_DEFAULT),
                        new RejectRedeliveredMessageMiddleware(),
                        new DispatchAfterCurrentBusMiddleware(),
                        new FailedMessageProcessingMiddleware(),
                        new SendMessageMiddleware(new SendersLocator([], $app)),
                        new HandleMessageMiddleware($app->make(HandlersLocator::class)),
                    ]
                );
                return $bus;
            });

            $manager->addBus(MessageBusManager::BUS_DEFAULT_ASYNC, function() use ($app, $sendersLocator) {
                $bus = new MessageBus(
                    [
                        new AddBusNameStampMiddleware(MessageBusManager::BUS_DEFAULT_ASYNC),
                        new RejectRedeliveredMessageMiddleware(),
                        new DispatchAfterCurrentBusMiddleware(),
                        new FailedMessageProcessingMiddleware(),
                        new SendMessageMiddleware($app->make(CoreDoctrineSendersLocator::class)),
                        new HandleMessageMiddleware($app->make(HandlersLocator::class)),
                    ]
                );
                return $bus;
            });
            return $manager;
        });

        $this->app->singleton(MessageBusInterface::class, function(Application $app) {
            return $app->make(MessageBusManager::class)->getBus(MessageBusManager::BUS_DEFAULT);
        });


        /*


        $this->app->singleton('messenger/bus/command',
            function ($app) use ($senders) {
            }
        );
        */
    }
}