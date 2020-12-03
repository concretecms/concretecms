<?php

namespace Concrete\Core\Notification\Mercure\Update;

class TestConnection implements UpdateInterface
{

    /**
     * @var string
     */
    protected $ping;

    public function __construct(string $ping)
    {
        $this->ping = $ping;
    }

    public function getTopicURL(): string
    {
        return 'https://global.concretecms.com/test-connection';
    }

    public function getData(): array
    {
        return ['pong' => $this->ping];
    }

}

