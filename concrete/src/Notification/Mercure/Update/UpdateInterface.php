<?php

namespace Concrete\Core\Notification\Mercure\Update;

/**
 * An Update in mercure is message sent that combines the topic (channel) sent to with some data.
 */
interface UpdateInterface
{

    public function getTopicURL(): string;

    public function getData(): array;

}

