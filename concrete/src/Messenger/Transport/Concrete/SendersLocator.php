<?php

namespace Concrete\Core\Messenger\Transport\Concrete;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\MessageBusManager;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

/**
 * This represents the default async transport in Concrete. That means it's doctrine-backed, with some custom
 * connection config for database tables. Other transports may be used.
 */
class SendersLocator implements SendersLocatorInterface
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

    public function getSenders(Envelope $envelope): iterable
    {
        $doctrineConnection = $this->connection->getWrappedConnection();

        return [
            MessageBusManager::BUS_DEFAULT_ASYNC => $this->app->make(DoctrineTransport::class, [
                'connection' => $doctrineConnection
            ])
        ];
    }

}
