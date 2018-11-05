<?php
namespace Concrete\Core\Notification\Notifier;

use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Entity\Notification\NotificationAlert;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;
use Concrete\Core\Notification\Type\TypeInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Access\ListItem\NotifyInNotificationCenterNotificationListItem;
use Concrete\Core\Permission\Key\Key;
use Doctrine\ORM\EntityManagerInterface;

class StandardNotifier implements NotifierInterface
{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUsersToNotify(SubscriptionInterface $subscription, SubjectInterface $subject)
    {
        $key = Key::getByHandle('notify_in_notification_center');
        $users = array();
        if (is_object($key)) {
            $access = $key->getPermissionAssignmentObject()->getPermissionAccessObject();
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
                        ($item->getSubscriptionsAllowedPermission() == 'C' && in_array($subscription->getSubscriptionIdentifier(), $item->getSubscriptionsAllowedArray()))) {
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
                $usersToRemove = array();
                foreach($subject->getUsersToExcludeFromNotification() as $user) {
                    $usersToRemove[] = $user->getUserID();
                }
                foreach($items as $item) {
                    if ($item->getSubscriptionsAllowedPermission() == 'N' ||
                        ($item->getSubscriptionsAllowedPermission() == 'C' && in_array($subscription->getSubscriptionIdentifier(), $item->getSubscriptionsAllowedArray()))) {
                        /**
                         * @var $entity Entity
                         */
                        $entity = $item->getAccessEntityObject();
                        foreach($entity->getAccessEntityUsers($access) as $user) {
                            $usersToRemove[] = $user->getUserID();
                        }
                    }
                }

                $users = array_unique($users);
                $usersToRemove = array_unique($usersToRemove);

                $users = array_filter($users, function($element) use ($usersToRemove) {
                    if (in_array($element->getUserID(), $usersToRemove)) {
                        return false;
                    }
                    return true;
                });

            }
        }
        return $users;
    }

    public function notify($users, Notification $notification)
    {
        foreach($users as $user) {

            $this->entityManager->persist($notification);

            $alert = new NotificationAlert();
            $alert->setUser($user->getEntityObject());
            $alert->setNotification($notification);
            $this->entityManager->persist($alert);
        }

        $this->entityManager->flush();
    }


}
