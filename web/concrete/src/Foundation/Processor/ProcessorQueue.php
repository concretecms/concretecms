<?php
namespace Concrete\Core\Foundation\Processor;

class ProcessorQueue extends Processor
{
    protected $itemsPerBatch = 20;
    protected $tasks = array();
    protected $queue;

    /**
     * @return int
     */
    public function getItemsPerBatch()
    {
        return $this->itemsPerBatch;
    }

    /**
     * @param int $itemsPerBatch
     */
    public function setItemsPerBatch($itemsPerBatch)
    {
        $this->itemsPerBatch = $itemsPerBatch;
    }

    public function setQueue(\ZendQueue\Queue $queue)
    {
        $this->queue = $queue;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function __sleep()
    {
        return array('itemsPerBatch', 'tasks');
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
        $this->getQueue()->deleteQueue();
        $tasks = $this->getTasks();
        foreach ($tasks as $task) {
            $action = new QueueAction($this, $this->target, $task[1]);
            $action->finish();
        }
    }

    public function execute(ActionInterface $action)
    {
        $action->execute();
        $this->getQueue()->deleteMessage($action->getQueueItem());
        if ($this->getQueue()->count() == 0) {
            $this->finish();
        }
    }

    public function getTotalTasks()
    {
        return $this->getQueue()->count();
    }

    /**
     * Takes the current queue, and fills it based on the currently registered tasks and the
     * registered processor.
     */
    public function process()
    {
        $queue = $this->getQueue();
        $tasks = $this->getTasks();
        foreach ($this->target->getItems() as $targetItem) {
            foreach ($tasks as $task) {
                $action = new QueueAction($this, $this->target, $task[1], $targetItem);
                $queue->send(serialize($action));
            }
        }
    }
}
