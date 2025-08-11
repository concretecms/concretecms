<?php
namespace Concrete\Core\System\Status;

use Concrete\Core\Application\Application;

class QueueStatusQueue implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $queue;

    /**
     * @var int
     */
    protected $count;

    public function __construct($queue, $count = 0)
    {
        $this->queue = $queue;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }



    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->getQueue(),
            'count' => $this->getCount(),
        ];
    }

}
