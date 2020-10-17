<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Messenger\HandlersLocator;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Concrete\Core\Database\Connection\Connection as DoctrineConnection;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class MessengerServiceProvider extends ServiceProvider
{

    const BUS_NAME_COMMAND = 'command';

    const TRANSPORT_ASYNC = 'async';

    public function register()
    {

        $this->app->singleton(SerializerInterface::class, Serializer::class);

        $doctrineConnection = new Connection([], $this->app->make(DoctrineConnection::class));

        $senders = [
            'async' => $this->app->make(DoctrineTransport::class, [
                'connection' => $doctrineConnection
            ])
        ];

        $this->app->singleton('messenger/bus/command',
            function ($app) use ($senders) {
                $bus = new MessageBus(
                    [
                        new AddBusNameStampMiddleware(self::BUS_NAME_COMMAND),
                        new RejectRedeliveredMessageMiddleware(),
                        new DispatchAfterCurrentBusMiddleware(),
                        new FailedMessageProcessingMiddleware(),
                        new SendMessageMiddleware(new SendersLocator($senders, $app)),
                        new HandleMessageMiddleware($app->make(HandlersLocator::class)),
                    ]
                );
                return $bus;
            }
        );
        $this->app->singleton(MessageBusInterface::class, 'messenger/bus/command');
    }
}