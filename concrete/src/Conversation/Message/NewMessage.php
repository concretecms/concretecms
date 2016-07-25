<?php
namespace Concrete\Core\Conversation\Message;

use Concrete\Core\Notification\Subject\SubjectInterface;

class NewMessage implements SubjectInterface
{

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getConversationMessage()
    {
        return $this->message;
    }

    public function getNotificationDate()
    {
        return $this->message->getConversationMessageDateTime();
    }

    public function getUsersToExcludeFromNotification()
    {
        return array($this->message->getConversationMessageAuthorObject()->getUser());
    }

}
