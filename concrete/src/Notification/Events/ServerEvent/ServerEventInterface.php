<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

/**
 * Responsible for translating a Concrete server event object into something the Mercure Hub can work with natively
 */
interface ServerEventInterface
{

    /**
     * @return string[]
     */
    public function getTopics(): array;

    /**
     * @return string
     */
    public function getData(): string;

}

