<?php
namespace Concrete\Core\Conversation\Message;

use Concrete\Core\Notification\Subject\SubjectInterface;

/**
 * @deprecated This class has never been used by the core: the conversation messages (\Concrete\Core\Conversation\Message\Message) are notification subjects themselves, and they are what the core passes to the notification type
 */
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
