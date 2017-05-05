<?php
namespace Concrete\Core\Express\Entry\Notifier;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface NotifierInterface
{

    function getNotificationList();

    function sendNotifications(NotificationListInterface $notifications, Entry $entry, $updateType);

}