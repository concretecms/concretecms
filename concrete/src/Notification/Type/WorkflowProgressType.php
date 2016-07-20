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

    public function getSubscriptions()
    {
        $subscription = new StandardSubscription('workflow_progress', t('Workflow notifications'));
        return array($subscription);
    }


}