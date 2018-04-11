<?php

namespace Concrete\Core\Job\Command;

use Concrete\Core\Foundation\Bus\Command\CommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class ExecuteJobItemCommand implements QueueableCommand, CommandInterface
{

    protected $queueName;

    protected $data;

    protected $jobHandle;

    public function __construct($queueName, $jobHandle, $data)
    {
        $this->queueName = $queueName;
        $this->data = $data;
        $this->jobHandle = $jobHandle;
    }

    /**
     * @return mixed
     */
    public function getJobHandle()
    {
        return $this->jobHandle;
    }

    /**
     * @param mixed $jobHandle
     */
    public function setJobHandle($jobHandle)
    {
        $this->jobHandle = $jobHandle;
    }


    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @param mixed $queueName
     */
    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;
    }


    public function getName()
    {
        return $this->getQueueName();
    }

}