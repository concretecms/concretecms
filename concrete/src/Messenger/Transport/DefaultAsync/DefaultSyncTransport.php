<?php
namespace Concrete\Core\Messenger\Transport\DefaultAsync;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Transport\Sync\SyncTransport;

class DefaultSyncTransport implements TransportInterface
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getSenders(): iterable
    {
        return ['sync' => function() {
            return $this->app->make(SyncTransport::class);
        }];
    }

    public function getReceivers(): iterable
    {
        return ['sync' => function() {
            return $this->app->make(SyncTransport::class);
        }];
    }

}