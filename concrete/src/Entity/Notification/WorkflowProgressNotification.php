<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Notification\View\WorkflowProgressListView;
use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Workflow\Progress\SiteProgressInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="WorkflowProgressNotifications"
 * )
 */
class WorkflowProgressNotification extends Notification
{
    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int
     */
    protected $wpID;

    /**
     * @var \Concrete\Core\Workflow\Progress\Progress|null
     */
    protected $progressObject;

    public function __construct(Progress $progress)
    {
        $this->wpID = $progress->getWorkflowProgressID();
        $this->progressObject = $progress;
        parent::__construct($progress);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new WorkflowProgressListView($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getNotificationDateTimeZone()
     */
    public function getNotificationDateTimeZone()
    {
        $progress = $this->getWorkflowProgressObject();
        if ($progress instanceof SiteProgressInterface) {
            $site = $progress->getSite();
            if ($site) {
                return $site->getTimezone();
            }
        }

        return null;
    }

    /**
     * @return \Concrete\Core\Workflow\Progress\Progress|null may be NULL if the progress object has been deleted
     */
    public function getWorkflowProgressObject()
    {
        if ($this->progressObject === null) {
            $this->progressObject = Progress::getByID($this->wpID) ?: null;
        }

        return $this->progressObject;
    }
}
