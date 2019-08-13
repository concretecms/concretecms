<?php
namespace Concrete\Core\Foundation\Processor;

/**
 * @since 5.7.5.3
 */
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
