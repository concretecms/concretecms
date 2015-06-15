<?php
namespace Concrete\Core\Foundation\Processor;

use Concrete\Core\User\Search\Result\Item;

class Processor
{

    protected $itemsPerBatch = 20;
    protected $tasks = array();
    protected $queue;
    protected $target;

    public function __construct(TargetInterface $target)
    {
        $this->target = $target;
    }

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

    public function receive()
    {
        $queueItems = $this->getQueue()->receive($this->itemsPerBatch);
        $items = array();
        foreach($queueItems as $queueItem) {
            $action = unserialize($queueItem->body);
            $action->setQueueItem($queueItem);
            $items[] = $action;
        }
        return $items;
    }

    public function execute(Action $action)
    {
        $action->execute();
        $this->getQueue()->deleteMessage($action->getQueueItem());
        if ($this->getQueue()->count() == 0) {
            $this->getQueue()->deleteQueue();
        }
    }

    public function registerTask(TaskInterface $task, $priority = 0)
    {
        $this->tasks[] = array($priority, $task);
    }

    /**
     * Takes the current queue, and fills it based on the currently registered tasks and the
     * registered processor.
     */
    public function start()
    {
        $queue = $this->getQueue();
        $tasks = $this->tasks;
        usort($tasks, function($a, $b) {
            if ($a[0] == $b[0]) {
                return 0;
            }
            return ($a[0] < $b[0]) ? -1 : 1;
        });

        foreach($this->target->getItems() as $targetItem) {
            foreach($tasks as $task) {
                $action = new Action($this->target, $task[1], $targetItem);
                $queue->send(serialize($action));
            }
        }
    }

}
