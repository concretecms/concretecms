<?php

namespace Concrete\Core\Foundation\Queue\Mutex;

use Bernard\Queue;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Queue\RoundRobinQueue;
use Concrete\Core\System\Mutex\MutexInterface;

class MutexGeneratorFactory
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, MutexKeyGenerator $keyGenerator)
    {
        $this->app = $app;
        $config = $this->app->make('config');
        $mutexes = $config->get('app.mutex');
        foreach($config->get('app.commands') as $entry) {
            if ($entry[2] ?? null) {
                $mutexes[$keyGenerator->getMutexKey($entry[2])] = true;
            }
        }

        $jobs = \Job::getList();
        foreach($jobs as $job) {
            if ($job->supportsQueue()) {
                $mutexes[$keyGenerator->getMutexKey($job)] = true;
            }
        }
        $config->set('app.mutex', $mutexes);
    }

    public function create(Queue $queue)
    {
        if ($queue instanceof RoundRobinQueue) {
            $class = RoundRobinQueueMutexGenerator::class;
        } else {
            $class = QueueMutexGenerator::class;
        }

        return $this->app->make($class);
    }

}