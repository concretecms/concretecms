<?php
namespace Concrete\Core\Express\Entry\Notifier;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

/**
 * @since 8.2.0
 */
interface NotificationListInterface
{
    public function addNotification(NotificationInterface $notification);

    /**
     * @return NotificationInterface[]
     */
    public function getNotifications();
}
