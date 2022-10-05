<?php

namespace Concrete\Core\Entity\Notification;

use Concrete\Core\Notification\Subject\SubjectInterface;
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

    /**
     * @param \Concrete\Core\Conversation\Message\NewMessage $message
     */
    public function __construct(SubjectInterface $message)
    {
        $this->cnvMessageID = $message->getConversationMessage()->getConversationMessageID();
        parent::__construct($message);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Entity\Notification\Notification::getListView()
     */
    public function getListView()
    {
        return null;
    }
}
