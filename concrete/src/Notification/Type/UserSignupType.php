<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\UserSignupNotification;
use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class UserSignupType extends Type
{

    /**
     * @param $user UserSignup
     */
    public function createNotification(SubjectInterface $user)
    {
        return new UserSignupNotification($user);
    }

    public function getSubscriptions()
    {
        $subscription = new StandardSubscription('user_signup', t('User signups'));
        return array($subscription);
    }




}