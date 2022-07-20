<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

class TestConnectionEvent extends AbstractConcreteEvent
{

    /**
     * @var string
     */
    protected $ping;

    public function __construct(string $ping)
    {
        $this->ping = $ping;
    }

    protected static function getEvent(): string
    {
        return 'TestConnection';
    }

    protected function getEventData()
    {
        return ['pong' => $this->ping];
    }

}

