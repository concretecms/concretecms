<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Symfony\Component\Mercure\Update;

/**
 * Responsible for translating a Concrete server event object into something the Mercure Hub can work with natively
 */
interface ServerEventInterface
{

    public function getUpdate(): Update;

}

