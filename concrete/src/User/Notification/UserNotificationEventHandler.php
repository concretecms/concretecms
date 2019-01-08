<?php

namespace Concrete\Core\User\Notification;

use Concrete\Core\Entity\Notification\UserDeactivatedNotification;
use Concrete\Core\Notification\Type\Manager;
use Concrete\Core\Notification\Type\UserDeactivatedType;
use Concrete\Core\User\Event\DeactivateUser;

/**
 * This method is less of a "service" and more of a controller
 * It deals with deactivated user events
 */
class UserNotificationEventHandler
{

    /**
     * The notification manager we send notifications with
     *
     * @var \Concrete\Core\Notification\Type\Manager
     */
    protected $notificationManager;

    public function __construct(Manager $manager)
    {
        $this->notificationManager = $manager;
    }

    /**
     * Handle deactivated user events
     *
     * @param \Concrete\Core\User\Event\DeactivateUser $event
     */
    public function deactivated(DeactivateUser $event)
    {
        /** @var UserDeactivatedType $type */
        $type = $this->notificationManager->driver(UserDeactivatedType::IDENTIFIER);
        $notifier = $type->getNotifier();

        if (method_exists($notifier, 'notify')) {
            $subscription = $type->getSubscription($event);
            $users = $notifier->getUsersToNotify($subscription, $event);
            $notification = new UserDeactivatedNotification($event);
            $notifier->notify($users, $notification);
        }
    }
}
