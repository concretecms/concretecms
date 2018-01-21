<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Event\EnvelopeEvent;
use Concrete\Core\Application\Application;
use Concrete\Core\Events\Broadcast\BroadcastSerializer;

class BernardEventsBroadcastSerializer implements BroadcastSerializer
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $mixed EnvelopeEvent
     */
    public function serializeForBroadcast($mixed)
    {
        $envelope = $mixed->getEnvelope();
        $serializer = $this->app->make('queue/serializer');
        return $serializer->serialize($envelope);
    }
}
