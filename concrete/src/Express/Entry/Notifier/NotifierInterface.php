<?php
namespace Concrete\Core\Express\Entry\Notifier;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

/**
 * @since 8.2.0
 */
interface NotifierInterface
{
    public function getNotificationList();

    public function sendNotifications(NotificationListInterface $notifications, Entry $entry, $updateType);
}
