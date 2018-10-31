<?php
namespace Concrete\Core\Notification\Subject;

interface SubjectInterface
{

    /**
     * Get the date of this notification
     *
     * @return \DateTime
     */
    public function getNotificationDate();

    /**
     * Get the users that should be excluded from notifications
     * Expected return value would be users involved in the creation of the notification, they may not need to be
     * notified.
     *
     * @return \Concrete\Core\Entity\User\User[]
     */
    public function getUsersToExcludeFromNotification();

}
