<?php

namespace Concrete\Core\Messenger\Transport\Sender;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection as DoctrineConnection;
use Concrete\Core\Messenger\MessageBusManager;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class ConcreteDoctrineSendersLocator implements SendersLocatorInterface
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function getSenders(Envelope $envelope): iterable
    {
        $doctrineConnection = new Connection([
            'table_name' => 'MessengerMessages',
            'queue_name' => 'default',
            'redeliver_timeout' => 3600,
            'auto_setup' => true,
        ], $this->app->make(DoctrineConnection::class));

        return [
            MessageBusManager::BUS_DEFAULT_ASYNC => $this->app->make(DoctrineTransport::class, [
                'connection' => $doctrineConnection
            ])
        ];
    }

}
