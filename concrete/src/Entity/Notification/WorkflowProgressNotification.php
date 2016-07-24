<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\UserSignupListView;
use Concrete\Core\Workflow\Progress\Progress;
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
     */
    protected $wpID;

    /**
     * @param $signup Progress
     */
    public function __construct(Progress $progress)
    {
        $this->wpID = $progress->getWorkflowProgressID();
        parent::__construct($progress);
    }


    public function getListView()
    {
        return new UserSignupListView($this);
    }


}
