<?php
namespace Concrete\Core\Notification\Notifier;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;

/**
 * @since 8.0.0
 */
interface NotifierInterface
{

    /**
     * Get a list of users to notify
     *
     * @param \Concrete\Core\Notification\Subscription\SubscriptionInterface $subscription
     * @param \Concrete\Core\Notification\Subject\SubjectInterface $subject
     *
     * @return \Concrete\Core\Entity\User\User[]
     */
    public function getUsersToNotify(SubscriptionInterface $subscription, SubjectInterface $subject);

}
