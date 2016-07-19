<?php
namespace Concrete\Core\Notification\Factory;


use Concrete\Core\Entity\Notification\NewConversationMessageNotification;
use Concrete\Core\Entity\Notification\UserSignupNotification;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Subject\SubjectInterface;

class NewConversationMessageFactory implements FactoryInterface
{

    /**
     * @param $user UserSignup
     */
    public function createNotification(SubjectInterface $message)
    {
        return new NewConversationMessageNotification($message);
    }

}
