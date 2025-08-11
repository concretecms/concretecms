<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

/**
 * Used by all core events that want to interact with Mercure. Translates event names into
 * topics, etc...
 */
abstract class AbstractConcreteEvent extends AbstractEvent
{

    protected function isPrivate()
    {
        return true;
    }

}

