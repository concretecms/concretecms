<?php
namespace Concrete\Core\Express\Entry\Notifier;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

class StandardNotifier extends AbstractNotifier
{

    protected $notifications;

    public function __construct()
    {
        $this->notifications = new NotificationList();
    }

    public function getNotificationList()
    {
        return $this->notifications;
    }

    public function sendNotifications(NotificationListInterface $notifications, Entry $entry, $updateType)
    {
        foreach($notifications->getNotifications() as $notification) {
            $notification->notify($entry, $updateType);
        }
    }


}