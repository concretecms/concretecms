<?php
namespace Concrete\Core\Notification;

use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Access\ListItem\NotifyInNotificationCenterNotificationListItem;
use Concrete\Core\Permission\Key\Key;
use Doctrine\ORM\EntityManagerInterface;

class Notifier
{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUsersToNotify(SubscriptionInterface $subscription)
    {
        $key = Key::getByHandle('notify_in_notification_center');
        $access = $key->getPermissionAssignmentObject()->getPermissionAccessObject();
        $users = array();
        if (is_object($access)) {
            /**
             * @var $access Access
             */
            $items = $access->getAccessListItems(Key::ACCESS_TYPE_INCLUDE);
            /**
             * @var $item NotifyInNotificationCenterNotificationListItem
             */
            foreach($items as $item) {
                if ($item->getSubscriptionsAllowedPermission() == 'A' ||
                    ($item->getSubscriptionsAllowedPermission() == 'C' && in_array($subscription, $item->getSubscriptionsAllowedArray()))) {
                    /**
                     * @var $entity Entity
                     */
                    $entity = $item->getAccessEntityObject();
                    $users = array_merge($entity->getAccessEntityUsers($access), $users);
                }
            }

            // Now we loop through the array and remove
            $items = $access->getAccessListItems(Key::ACCESS_TYPE_EXCLUDE);
            /**
             * @var $item NotifyInNotificationCenterNotificationListItem
             */
            foreach($items as $item) {
                if ($item->getSubscriptionsAllowedPermission() == 'N' ||
                    ($item->getSubscriptionsAllowedPermission() == 'C' && in_array($subscription, $item->getSubscriptionsAllowedArray()))) {
                    /**
                     * @var $entity Entity
                     */
                    $entity = $item->getAccessEntityObject();
                    $usersToRemove = $entity->getAccessEntityUsers($access);
                    $users = array_filter($users, function($element) use ($usersToRemove) {
                        if (in_array($element, $usersToRemove)) {
                            return false;
                        }
                        return true;
                    });
                }
            }

            $users = array_unique($users);
            return $users;
        }
    }

    public function notify($users, Notification $notification)
    {
        foreach($users as $user) {
            $notification->getUsersToAlert()->add($user->getEntityObject());
        }
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }


}
