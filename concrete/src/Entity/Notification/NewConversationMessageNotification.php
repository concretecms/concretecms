<?php
namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Conversation\Message\NewMessage;
use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\UserSignupListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ConversationMessageNotifications"
 * )
 */
class NewConversationMessageNotification extends Notification
{

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $cnvMessageID;

    /**
     * UserSignupNotification constructor.
     * @param $message NewMessage
     */
    public function __construct(SubjectInterface $message)
    {
        $this->cnvMessageID = $message->getConversationMessage()->getConversationMessageID();
        parent::__construct($message);
    }

    public function getListView()
    {

    }


}
