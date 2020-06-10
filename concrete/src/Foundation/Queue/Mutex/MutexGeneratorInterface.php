<?php

namespace Concrete\Core\Foundation\Queue\Mutex;

use Bernard\Queue;

interface MutexGeneratorInterface
{

    public function execute(Queue $queue, callable $callback);

}