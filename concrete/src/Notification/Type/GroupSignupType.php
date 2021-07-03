<?php

namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\GroupSignupNotification;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class GroupSignupType extends Type
{

    public function createNotification(SubjectInterface $group)
    {
        return new GroupSignupNotification($group);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('group_signup', t('Group signups'));
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
            new StandardFilter($this, 'group_signup', t('Group signups'),
                'groupsignupnotification')
        ];
    }

}