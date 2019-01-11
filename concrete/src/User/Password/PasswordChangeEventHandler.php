<?php

namespace Concrete\Core\User\Password;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Entry\User\ChangeUserPassword;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\User\Event\UserInfoWithPassword;
use Concrete\Core\User\Logger;
use Concrete\Core\User\User;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Event;

class PasswordChangeEventHandler
{


    /**
     * @var \Concrete\Core\User\Logger $logger
     */
    protected $logger;

    /**
     * The usage tracker we're using to track passwords
     *
     * @var \Concrete\Core\User\Password\PasswordUsageTracker
     */
    protected $tracker;

    public function __construct(Logger $logger, PasswordUsageTracker $tracker)
    {
        $this->logger = $logger;
        $this->tracker = $tracker;
    }

    /**
     * Event handler for `on_user_change_password` events
     *
     * @param \Symfony\Component\EventDispatcher\Event $event
     *
     * @throws \InvalidArgumentException If the given event is not the proper subclass type. Must be `UserInfoWithPassword`
     */
    public function handleEvent(Event $event)
    {
        if (!$event instanceof UserInfoWithPassword) {
            throw new InvalidArgumentException(t('Invalid event type provided. Event type must be "UserInfoWithPassword".'));
        }

        // Log the change
        $applier = new User();
        $this->logger->logChangePassword($event->getUserInfoObject()->getUserObject(), $applier);

        // Track the password use
        $this->tracker->trackUse($event->getUserPassword(), $event->getUserInfoObject());
    }
}
