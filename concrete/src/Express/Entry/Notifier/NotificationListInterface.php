<?php
namespace Concrete\Core\Express\Entry\Notifier;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface NotificationListInterface
{

    function addNotification(NotificationInterface $notification);

    /**
     * @return NotificationInterface[]
     */
    function getNotifications();

}