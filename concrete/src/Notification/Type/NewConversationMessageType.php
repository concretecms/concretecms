<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Entity\Notification\NewConversationMessageNotification;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Doctrine\ORM\Mapping as ORM;

class NewConversationMessageType extends Type
{

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Notification\Type\TypeInterface::createNotification()
     *
     * @param \Concrete\Core\Conversation\Message\Message $message
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


    public function getAvailableFilters()
    {
        return [
            new StandardFilter($this, 'new_conversation_message', t('Conversation messages'),
                'newconversationmessagenotification')
        ];
    }

}