<?php
namespace Concrete\Core\Express\Entry\Notifier;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

class NotificationList implements NotificationListInterface
{

    /**
     * @var NotificationInterface[]
     */
    protected $notifications = [];

    public function addNotification(NotificationInterface $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * @return array
     */
    public function getNotifications()
    {
        return $this->notifications;
    }



}