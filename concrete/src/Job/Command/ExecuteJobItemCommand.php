<?php

namespace Concrete\Core\Job\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class ExecuteJobItemCommand implements BatchableCommandInterface
{

    /**
     * @var string
     */
    protected $batchHandle;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var string
     */
    protected $jobHandle;

    public function __construct($batchHandle, $jobHandle, $data)
    {
        $this->batchHandle = $batchHandle;
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
     * @return string
     */
    public function getBatchHandle(): string
    {
        return $this->batchHandle;
    }

    /**
     * @param string $batchHandle
     */
    public function setBatchHandle(string $batchHandle): void
    {
        $this->batchHandle = $batchHandle;
    }

}