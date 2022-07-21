<?php

namespace Concrete\Core\Notification\Events\Topic;

/**
 * All topics that work with the core CMS should use this. It's just a convenience method so that we don't
 * have to always remember and pass our canonical URL
 */
class ConcreteTopic extends AbstractTopic
{

    const CORE_TOPIC_NAMESPACE = '/concrete/events';

    protected function getBaseTopicUrl(): string
    {
        $baseTopicUrl = parent::getBaseTopicUrl();
        return $baseTopicUrl . self::CORE_TOPIC_NAMESPACE;
    }


}

