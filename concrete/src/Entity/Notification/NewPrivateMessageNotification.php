<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Notification\View\NewPrivateMessageListView;
use Concrete\Core\User\PrivateMessage\PrivateMessage;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PrivateMessageNotifications"
 * )
 */
class NewPrivateMessageNotification extends Notification
{
    /**
     * @var \Concrete\Core\User\PrivateMessage\PrivateMessage|null
     */
    protected $message;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     *
     * @var int
     */
    protected $msgID;

    public function __construct(PrivateMessage $message)
    {
        $this->msgID = $message->getMessageID();
        $this->message = $message;
        parent::__construct($message);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new NewPrivateMessageListView($this);
    }

    /**
     * @return \Concrete\Core\User\PrivateMessage\PrivateMessage|null may be NULL if the message has been deleted
     */
    public function getMessageObject()
    {
        if ($this->message === null) {
            $this->message = PrivateMessage::getByID($this->msgID);
        }

        return $this->message;
    }
}
