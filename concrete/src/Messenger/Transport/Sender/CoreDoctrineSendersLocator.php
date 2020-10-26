<?php

namespace Concrete\Core\Messenger\Transport\Sender;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection as DoctrineConnection;
use Concrete\Core\Messenger\MessageBusManager;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class CoreDoctrineSendersLocator implements SendersLocatorInterface
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getSenders(Envelope $envelope): iterable
    {
        $doctrineConnection = new Connection([], $this->app->make(DoctrineConnection::class));

        return [
            MessageBusManager::BUS_DEFAULT_ASYNC => $this->app->make(DoctrineTransport::class, [
                'connection' => $doctrineConnection
            ])
        ];
    }

}
