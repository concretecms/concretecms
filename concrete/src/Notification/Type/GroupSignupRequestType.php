<?php

namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\GroupSignupRequestNotification;
use Concrete\Core\Entity\User\GroupSignupRequest;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class GroupSignupRequestType extends Type
{

    /**
     * @param GroupSignupRequest $groupSignupRequest
     * @return GroupSignupRequestNotification
     */
    public function createNotification(SubjectInterface $groupSignupRequest)
    {
        return new GroupSignupRequestNotification($groupSignupRequest);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('group_signup_request', t('Group signup requests'));
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
            new StandardFilter($this, 'group_signup_request', t('Group signup requests'),
                'groupsignuprequestnotification')
        ];
    }

}