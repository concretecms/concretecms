<?php
namespace Concrete\Core\Notification\Notifier;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;
use Concrete\Core\User\PrivateMessage\PrivateMessage;
use Concrete\Core\Workflow\Progress\Progress;

/**
 * @since 8.0.0
 */
class NewPrivateMessageNotifier extends StandardNotifier
{


    /**
     * @param SubscriptionInterface $subscription
     * @param PrivateMessage $subject
     */
    public function getUsersToNotify(SubscriptionInterface $subscription, SubjectInterface $subject)
    {
        return array($subject->getMessageUserToObject());
    }

}
