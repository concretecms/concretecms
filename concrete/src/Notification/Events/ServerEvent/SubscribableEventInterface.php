<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\TopicInterface;

interface SubscribableEventInterface
{

    public static function getTopicForSubscribing(): TopicInterface;

}

