<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\WorkflowProgressNotification;
use Concrete\Core\Notification\Alert\Filter\WorkflowFilter;
use Concrete\Core\Notification\Alert\Filter\WorkflowProgressFilterFactory;
use Concrete\Core\Notification\Notifier\WorkflowProgressNotifier;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Workflow\Workflow;
use Doctrine\ORM\Mapping as ORM;

class WorkflowProgressType extends Type
{

    public function createNotification(SubjectInterface $subject)
    {
        return new WorkflowProgressNotification($subject);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('workflow_progress', t('Workflow notifications'));
        return $subscription;
    }

    public function getNotifier()
    {
        return new WorkflowProgressNotifier($this->entityManager);
    }

    public function getSubscription(SubjectInterface $subject)
    {
        return $this->createSubscription();
    }

    public function getAvailableSubscriptions()
    {
        return array($this->createSubscription());
    }

    public function clearNotification(Progress $progress)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Notification\WorkflowProgressNotification');
        $notification = $r->findOneBy(array('wpID' => $progress->getWorkflowProgressID()));
        if (is_object($notification)) {
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
        }
    }

    public function getAvailableFilters()
    {
        $workflows = Workflow::getList();
        $filters = [];
        foreach($workflows as $workflow) {
            $filters[] = new WorkflowFilter($workflow);

        }
        return $filters;
    }

}