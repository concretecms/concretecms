<?php
namespace Concrete\Core\Job;

use Concrete\Core\Application\Application;
use Concrete\Core\Job\Command\ExecuteJobItemCommand;
use Doctrine\ORM\EntityManager;

/**
 * Wrapper class for our batching specifically for use with jobs to minimize backward compatibility headaches.
 */
class JobQueue
{

    /**
     * @var QueueableJob
     */
    protected $job;

    public function __construct(QueueableJob $job)
    {
        $this->job = $job;
    }

    public function send($mixed)
    {
        // We need to be able to set the bus because sometimes we're running these synchronously - e.g from the
        // command line using c5:job
        $bus = $this->isAsynchronous() ? AsynchronousBus::getHandle() : SynchronousBus::getHandle();
        $data = serialize($mixed);
        $this->totalMessages++;
        $command = new ExecuteJobItemCommand($this->getBatch()->getBatchHandle(), $this->job->getJobHandle(), $data);
        return $this->app->getCommandDispatcher()->dispatch($command, $bus);
    }

}