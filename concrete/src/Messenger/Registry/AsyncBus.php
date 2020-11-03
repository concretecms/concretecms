<?php
namespace Concrete\Core\Messenger\Registry;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\HandlersLocator;
use Concrete\Core\Messenger\MessageBusManager;
use Concrete\Core\Messenger\Transport\Concrete\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineReceiver;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Concrete\Core\Messenger\Transport\Concrete\SendersLocator as ConcreteSendersLocator;
class AsyncBus implements RegistryInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Application $app, Connection $connection)
    {
        $this->app = $app;
        $this->connection = $connection;
    }

    public function getBusBuilder(string $handle): callable
    {
        return function() use ($handle) {
            $bus = new MessageBus(
                [
                    new AddBusNameStampMiddleware($handle),
                    new RejectRedeliveredMessageMiddleware(),
                    new DispatchAfterCurrentBusMiddleware(),
                    new FailedMessageProcessingMiddleware(),
                    new SendMessageMiddleware($this->app->make(ConcreteSendersLocator::class)),
                    new HandleMessageMiddleware($this->app->make(HandlersLocator::class)),
                ]
            );
            return $bus;
        };
    }

    public function getReceivers(): iterable
    {
        $connection = $this->connection;
        $app = $this->app;

        return [
            MessageBusManager::BUS_DEFAULT_ASYNC => function() use ($app, $connection) {
                return $app->make(
                    DoctrineReceiver::class,
                    [
                        'connection' => $connection->getWrappedConnection()
                    ]
                );
            }
        ];
    }
}