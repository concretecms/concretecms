<?php
namespace Concrete\Core\Messenger\Transport\DefaultFailed;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\Transport\FailedTransportInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineReceiver;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineSender;

class DefaultFailedTransport implements FailedTransportInterface
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var DefaultFailedConnection
     */
    protected $connection;

    public function __construct(Application $app, DefaultFailedConnection $connection)
    {
        $this->app = $app;
        $this->connection = $connection;
    }

    public function getSenders(): iterable
    {
        $connection = $this->connection;
        $app = $this->app;
        return [FailedTransportInterface::DEFAULT_FAILED => function() use ($connection, $app) {
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

        return [FailedTransportInterface::DEFAULT_FAILED => function() use ($app, $connection) {
            return $app->make(
                DoctrineReceiver::class,
                [
                    'connection' => $connection->getWrappedConnection()
                ]
            );
        }];
    }

}