<?php

namespace Concrete\Core\User\Password;

use Concrete\Core\User\Event\UserInfoWithPassword;
use InvalidArgumentException;

class PasswordChangeEventHandler
{


    /**
     * The usage tracker we're using to track passwords
     *
     * @var \Concrete\Core\User\Password\PasswordUsageTracker
     */
    protected $tracker;

    public function __construct(PasswordUsageTracker $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Event handler for `on_user_change_password` events
     *
     * @param object $event
     *
     * @throws \InvalidArgumentException If the given event is not the proper subclass type. Must be `UserInfoWithPassword`
     */
    public function handleEvent($event)
    {
        if (!$event instanceof UserInfoWithPassword) {
            throw new InvalidArgumentException(t('Invalid event type provided. Event type must be "UserInfoWithPassword".'));
        }

        // Track the password use
        $this->tracker->trackUse($event->getUserPassword(), $event->getUserInfoObject());
    }
}
