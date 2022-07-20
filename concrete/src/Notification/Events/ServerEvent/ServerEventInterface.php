<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

/**
 * Responsible for translating a Concrete server event object into something the Mercure Hub can work with natively
 */
interface ServerEventInterface
{

    public static function getTopics(): array;

    public function getData(): string;

}

