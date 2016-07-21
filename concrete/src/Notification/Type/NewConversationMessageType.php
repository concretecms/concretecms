<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Conversation\Message\NewMessage;
use Concrete\Core\Entity\Notification\NewConversationMessageNotification;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Doctrine\ORM\Mapping as ORM;

class NewConversationMessageType extends Type
{

    /**
     * @param $user NewMessage
     */
    public function createNotification(SubjectInterface $message)
    {
        return new NewConversationMessageNotification($message);
    }

    protected function createSubscription()
    {
        $subscription = new StandardSubscription('new_conversation_message', t('Conversation messages'));
        return $subscription;
    }

    public function getSubscription(SubjectInterface $subject)
    {
        return $this->createSubscription();
    }

    public function getAvailableSubscriptions()
    {
        return array($this->createSubscription());
    }


}