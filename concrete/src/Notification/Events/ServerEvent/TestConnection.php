<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

class TestConnection implements EventInterface
{

    /**
     * @var string
     */
    protected $ping;

    public function __construct(string $ping)
    {
        $this->ping = $ping;
    }

    public function getEvent(): string
    {
        return 'TestConnection';
    }

    public function getData(): array
    {
        return ['pong' => $this->ping];
    }

}

