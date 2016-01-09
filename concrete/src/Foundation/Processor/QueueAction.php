<?php
namespace Concrete\Core\Foundation\Processor;

class QueueAction extends Action
{

    protected $queueItem;

    public function setQueueItem($queueItem)
    {
        $this->queueItem = $queueItem;
    }

    public function getQueueItem()
    {
        return $this->queueItem;
    }
}
