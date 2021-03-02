<?php

namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\GroupSignupRequestAcceptNotification;
use Concrete\Core\Entity\User\GroupSignupRequestAccept;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class GroupSignupRequestAcceptType extends Type
{

    /**
     * @param GroupSignupRequestAccept $groupSignupRequestAccept
     * @return GroupSignupRequestAcceptNotification
     */
    public function createNotification(SubjectInterface $groupSignupRequestAccept)
    {
        return new GroupSignupRequestAcceptNotification($groupSignupRequestAccept);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('group_signup_request_accept', t('Group accepted signup requests'));
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
            new StandardFilter($this, 'group_signup_request_accept', t('Group accepted signup requests'),
                'groupsignuprequestacceptnotification')
        ];
    }

}