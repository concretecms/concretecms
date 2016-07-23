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
    public function createNotification(SubjectInterface $signup)
    {
        return new UserSignupNotification($signup);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('user_signup', t('User signups'));
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



}