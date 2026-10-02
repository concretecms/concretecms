<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;

class CoreUpdateType extends Type
{

    public function createNotification(SubjectInterface $subject)
    {
        throw new \RuntimeException(t('The core update notifications are not implemented yet.'));
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('core_update', t('Concrete updates'));
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
        return new StandardFilter($this, 'core_update', t('Concrete Updates'), 'coreupdatenotification');
    }

    public function getAvailableFilters()
    {
        return [$this->createFilter()];
    }


}