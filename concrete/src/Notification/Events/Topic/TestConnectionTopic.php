<?php

namespace Concrete\Core\Notification\Events\Topic;

class TestConnectionTopic extends ConcreteTopic
{

    public function __construct()
    {
        parent::__construct('/test_connection');
    }

}

