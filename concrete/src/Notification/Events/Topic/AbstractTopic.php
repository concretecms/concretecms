<?php

namespace Concrete\Core\Notification\Events\Topic;

abstract class AbstractTopic implements TopicInterface
{

    /**
     * @var string|null
     */
    protected $path;

    public function __construct(string $path = null)
    {
        $this->path = $path;
    }

    protected function getBaseTopicUrl(): string
    {
        $site = app('site')->getSite();
        return rtrim($site->getSiteCanonicalUrl(), '/');
    }

    public function getTopicUrl(): string
    {
        $url = $this->getBaseTopicUrl();
        if ($this->path) {
            $url .= '/' . trim($this->path, '/');
        }
        return $url;
    }

    public function __toString()
    {
        return $this->getTopicUrl();
    }


}

