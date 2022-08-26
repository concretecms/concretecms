<?php

namespace Concrete\Core\Notification\Events\Topic;

class ConcreteProcessTopic extends ConcreteTopic
{

    public function getBaseTopicUrl(): string
    {
        return parent::getBaseTopicUrl() . '/processes';
    }

}

