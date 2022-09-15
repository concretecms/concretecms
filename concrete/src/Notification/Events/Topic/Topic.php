<?php

namespace Concrete\Core\Notification\Events\Topic;

final class Topic extends AbstractTopic
{

    /**
     * @var string|null
     */
    protected $topic;

    public function __construct(string $topic = null)
    {
        $this->topic = $topic;
    }

    public function getTopicUrl(): string
    {
        return $this->topic;
    }


}

