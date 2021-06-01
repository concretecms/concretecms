<?php

namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\GroupSignupRequestDeclineNotification;
use Concrete\Core\Entity\User\GroupSignupRequestDecline;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class GroupSignupRequestDeclineType extends Type
{

    /**
     * @param GroupSignupRequestDecline $groupSignupRequestDecline
     * @return GroupSignupRequestDeclineNotification
     */
    public function createNotification(SubjectInterface $groupSignupRequestDecline)
    {
        return new GroupSignupRequestDeclineNotification($groupSignupRequestDecline);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('group_signup_request_decline', t('Group declined signup requests'));
        return $subscription;
    }

    public function getSubscription(SubjectInterface $subject)
    {
        return $this->createSubscription();
    }

    public function getAvailableSubscriptions()
    {
        return array($this->createSubscription());
    }


    public function getAvailableFilters()
    {
        return [
            new StandardFilter($this, 'group_signup_request_decline', t('Group declined signup requests'),
                'groupsignuprequestdeclinenotification')
        ];
    }

}