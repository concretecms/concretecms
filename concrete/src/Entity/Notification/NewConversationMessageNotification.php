<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Notification\View\NewConversationMessageListView;
use Concrete\Core\Notification\View\StandardListView;
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
     *
     * @var int
     */
    protected $cnvMessageID;

    public function __construct(Message $message)
    {
        $this->cnvMessageID = $message->getConversationMessageID();
        parent::__construct($message);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return new NewConversationMessageListView($this);
    }

    /**
     * @return int
     */
    public function getConversationMessageID(): int
    {
        return $this->cnvMessageID;
    }

    public function getConversationMessageObject()
    {
        return Message::getByID($this->getConversationMessageID());
    }



}
