<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Notification\Subject\SubjectInterface;

interface TypeInterface
{

    /**
     * @return mixed
     */
    function getSubscriptions();
    function createNotification(SubjectInterface $subject);


}