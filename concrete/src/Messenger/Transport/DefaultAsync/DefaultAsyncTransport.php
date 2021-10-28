<?php
namespace Concrete\Core\Messenger\Transport\DefaultAsync;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\MessageBusManager;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineReceiver;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineSender;

class DefaultAsyncTransport implements TransportInterface
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var DefaultAsyncConnection
     */
    protected $connection;

    public function __construct(Application $app, DefaultAsyncConnection $connection)
    {
        $this->app = $app;
        $this->connection = $connection;
    }

    public function getSenders(): iterable
    {
        $connection = $this->connection;
        $app = $this->app;
        return [TransportInterface::DEFAULT_ASYNC => function() use ($connection, $app) {
            return $app->make(
                DoctrineSender::class,
                [
                    'connection' => $connection->getWrappedConnection()
                ]
            );
        }];
    }

    public function getReceivers(): iterable
    {
        $connection = $this->connection;
        $app = $this->app;

        return [TransportInterface::DEFAULT_ASYNC => function() use ($app, $connection) {
            return $app->make(
                DoctrineReceiver::class,
                [
                    'connection' => $connection->getWrappedConnection()
                ]
            );
        }];
    }

}