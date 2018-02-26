<?php
namespace Concrete\Core\Foundation\Processor;

use Bernard\Queue;
use Concrete\Core\Foundation\Queue\QueueService;

class ProcessorQueue extends Processor
{
    protected $itemsPerBatch = 20;
    protected $tasks = array();
    protected $queue;
    protected $queueService;


    public function __construct(QueueService $queueService, TargetInterface $target)
    {
        $this->queueService = $queueService;
        parent::__construct($target);
    }

    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function __sleep()
    {
        return array('tasks');
    }

    public function receive()
    {
        $queueItems = $this->getQueue()->receive($this->itemsPerBatch);
        $items = array();
        foreach ($queueItems as $queueItem) {
            $action = unserialize($queueItem->body);
            $action->setQueueItem($queueItem);
            $items[] = $action;
        }

        return $items;
    }

    public function finish()
    {
        $tasks = $this->getTasks();
        foreach ($tasks as $task) {
            $action = new QueueAction($this, $this->target, $task[1]);
            $action->finish();
        }
    }

    /**
     * Takes the current queue, and fills it based on the currently registered tasks and the
     * registered processor.
     */
    public function process()
    {
        $tasks = $this->getTasks();
        foreach ($this->target->getItems() as $targetItem) {
            foreach ($tasks as $task) {
                $action = new QueueAction($this, $this->target, $task[1], $targetItem);
                $message = new ProcessorQueueMessage($action);
                $this->queueService->push($this->queue, $message);
            }
        }
    }
}
