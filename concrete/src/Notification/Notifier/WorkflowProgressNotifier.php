<?php
namespace Concrete\Core\Notification\Notifier;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;
use Concrete\Core\Workflow\Progress\Progress;

class WorkflowProgressNotifier extends StandardNotifier
{


    /**
     * @param SubscriptionInterface $subscription
     * @param Progress $subject
     */
    public function getUsersToNotify(SubscriptionInterface $subscription, SubjectInterface $subject)
    {
        $global = parent::getUsersToNotify($subscription, $subject);
        // $global is an array of users who should get this notification because
        // they get every notification. These are groups and users set in global
        // notification preferences/permissions

        // Now we determine the users who are supposed to receive this notification
        // because they actually have permission to do something about it.

        $workflow = $subject->getWorkflowObject();
        $relevant = $workflow->getWorkflowProgressApprovalUsers($subject);

        // Only add notification for those users who appear in BOTH arrays
        $notified = array_filter($relevant, function($element) use ($global) {
            if (in_array($element, $global)) {
                return true;
            }
            return false;
        });

        return $notified;
    }

}
