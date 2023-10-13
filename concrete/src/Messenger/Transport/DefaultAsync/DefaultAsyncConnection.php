<?php

namespace Concrete\Core\Messenger\Transport\DefaultAsync;

use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection as DoctrineTransportConnection;
use Concrete\Core\Application\Application;
use Doctrine\DBAL\Connection as DoctrineConnection;

/**
 * A decorator for the default Doctrine Transport Connection.
 */
class DefaultAsyncConnection
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var DoctrineTransportConnection
     */
    protected $wrappedConnection;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return DoctrineTransportConnection
     */
    public function getWrappedConnection(): DoctrineTransportConnection
    {
        if (!$this->wrappedConnection) {
            $this->wrappedConnection = new DoctrineTransportConnection(
                [
                    'table_name' => 'MessengerMessages',
                    'queue_name' => 'default',
                    'redeliver_timeout' => 3600,
                    'auto_setup' => true,
                ], $this->app->make(DoctrineConnection::class)
            );
        }
        return $this->wrappedConnection;
    }

}
