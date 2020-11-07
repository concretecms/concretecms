<?php

namespace Concrete\Core\Job\Command;

use Concrete\Core\Foundation\Command\Command;

class ExecuteJobItemCommand extends Command
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
