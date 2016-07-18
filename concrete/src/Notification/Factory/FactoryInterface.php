<?php
namespace Concrete\Core\Notification\Factory;

use Concrete\Core\Notification\Subject\SubjectInterface;

interface FactoryInterface
{

    function createNotification(SubjectInterface $subject);

}
