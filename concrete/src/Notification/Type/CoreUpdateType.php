<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class CoreUpdateType extends Type
{

    public function createNotification(SubjectInterface $subject)
    {
        // TODO: Implement createNotification() method.
    }

    public function getSubscriptions()
    {
        $subscription = new StandardSubscription('core_update', t('concrete5 updates'));
        return array($subscription);
    }




}