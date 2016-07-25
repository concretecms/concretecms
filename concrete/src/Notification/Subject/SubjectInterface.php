<?php
namespace Concrete\Core\Notification\Subject;

interface SubjectInterface
{

    function getNotificationDate();
    function getUsersToExcludeFromNotification();

}
