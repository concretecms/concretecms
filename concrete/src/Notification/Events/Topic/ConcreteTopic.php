<?php

namespace Concrete\Core\Notification\Events\Topic;

/**
 * All topics that work with the core CMS should use this. It's just a convenience method so that we don't
 * have to always remember and pass our canonical URL
 */
class ConcreteTopic implements TopicInterface
{

    const CORE_TOPIC_NAMESPACE = '/concrete/events';

    /**
     * @var string|null
     */
    protected $slug;

    public function __construct(string $slug = null)
    {
        $this->slug = $slug;
    }
    protected function getBaseTopicUrl(): string
    {
        $site = app('site')->getSite();
        return rtrim($site->getSiteCanonicalUrl(), '/') . self::CORE_TOPIC_NAMESPACE;
    }

    public function getTopicUrl(): string
    {
        $url = $this->getBaseTopicUrl();
        if ($this->slug) {
            $url .= '/' . $this->slug;
        }
        return $url;
    }

    public function __toString()
    {
        return $this->getTopicUrl();
    }


}

