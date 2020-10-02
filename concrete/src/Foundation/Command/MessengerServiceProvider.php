<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Messenger\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;

class MessengerServiceProvider extends ServiceProvider
{

    const BUS_NAME_COMMAND = 'command';

    public function register()
    {
        $this->app->singleton('messenger/bus/command',
            function ($app) {
                $bus = new MessageBus(
                    [
                        new AddBusNameStampMiddleware(self::BUS_NAME_COMMAND),
                        new RejectRedeliveredMessageMiddleware(),
                        new DispatchAfterCurrentBusMiddleware(),
                        new FailedMessageProcessingMiddleware(),
                        new SendMessageMiddleware(new SendersLocator([], $app)),
                        new HandleMessageMiddleware($app->make(HandlersLocator::class)),
                    ]
                );
                return $bus;
            }
        );
        $this->app->singleton(MessageBusInterface::class, 'messenger/bus/command');
    }
}