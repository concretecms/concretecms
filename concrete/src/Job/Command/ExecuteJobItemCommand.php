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
    protected $jobHandle;

    /**
     * @var mixed
     */
    protected $data;

    public function __construct(string $batchHandle, string $jobHandle, $data)
    {
        $this->setBatchHandle($batchHandle);
        $this->setJobHandle($jobHandle);
        $this->setData($data);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return $this->batchHandle;
    }

    /**
     * @return $this
     */
    public function setBatchHandle(string $batchHandle)
    {
        $this->batchHandle = $batchHandle;

        return $this;
    }

    public function getJobHandle(): string
    {
        return $this->jobHandle;
    }

    /**
     * @return $this
     */
    public function setJobHandle(string $jobHandle)
    {
        $this->jobHandle = $jobHandle;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return $this
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }
}
