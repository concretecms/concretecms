<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\NewFormSubmissionNotification;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class NewFormSubmissionType extends Type
{

    public function createNotification(SubjectInterface $subject)
    {
        return new NewFormSubmissionNotification($subject);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('new_form_submission', t('Form submissions'));
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