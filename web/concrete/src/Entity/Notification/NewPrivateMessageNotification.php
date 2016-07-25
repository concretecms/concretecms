<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\NewPrivateMessageListView;
use Concrete\Core\Notification\View\UserSignupListView;
use Concrete\Core\Notification\View\WorkflowProgressListView;
use Concrete\Core\User\PrivateMessage\PrivateMessage;
use Concrete\Core\Workflow\Progress\Progress;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PrivateMessageNotifications"
 * )
 */
class NewPrivateMessageNotification extends Notification
{

    protected $message;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $msgID;

    /**
     * @param $message PrivateMessage
     */
    public function __construct(PrivateMessage $message)
    {
        $this->msgID = $message->getMessageID();
        parent::__construct($message);
    }


    public function getListView()
    {
        return new NewPrivateMessageListView($this);
    }

    public function getMessageObject()
    {
        if (!isset($this->message)) {
            $this->message = PrivateMessage::getByID($this->msgID);
        }
        return $this->message;
    }


}
