<?php

namespace Concrete\Core\Notification\Events\Topic;

interface TopicInterface
{

    public function __toString();

    public function getTopicUrl(): string;

}

