<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\NewPrivateMessageNotification;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Notifier\NewPrivateMessageNotifier;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class NewPrivateMessageType extends Type
{

    public function createNotification(SubjectInterface $subject)
    {
        return new NewPrivateMessageNotification($subject);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('new_private_message', t('Private messages'));
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


    public function getNotifier()
    {
        return new NewPrivateMessageNotifier($this->entityManager);
    }

    public function getAvailableFilters()
    {
        return [
            new StandardFilter($this, 'new_private_message', t('Private messages'), 'newprivatemessagenotification')
        ];
    }


}