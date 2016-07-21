<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Doctrine\ORM\Mapping as ORM;

class WorkflowProgressType extends Type
{

    public function createNotification(SubjectInterface $subject)
    {
        // TODO: Implement createNotification() method.
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('workflow_progress', t('Workflow notifications'));
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