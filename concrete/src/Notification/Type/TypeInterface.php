<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Notification\Notifier\NotifierInterface;
use Concrete\Core\Notification\Subject\SubjectInterface;

interface TypeInterface
{

    /**
     * @return mixed
     */
    function getAvailableSubscriptions();
    function getSubscription(SubjectInterface $subject);
    function createNotification(SubjectInterface $subject);

    /**
     * @return NotifierInterface
     */
    function getNotifier();


}