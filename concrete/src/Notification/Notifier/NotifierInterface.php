<?php
namespace Concrete\Core\Notification\Notifier;

use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;

interface NotifierInterface
{

    function getUsersToNotify(SubscriptionInterface $subscription, SubjectInterface $subject);


}