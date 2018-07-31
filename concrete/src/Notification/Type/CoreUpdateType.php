<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class CoreUpdateType extends Type
{

    public function createNotification(SubjectInterface $subject)
    {
        // TODO: Implement createNotification() method.
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('core_update', t('concrete5 updates'));
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

    protected function createFilter()
    {
        return new StandardFilter($this, 'core_update', t('concrete5 Updates'), 'coreupdatenotification');
    }

    public function getAvailableFilters()
    {
        return [$this->createFilter()];
    }


}