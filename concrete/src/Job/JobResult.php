<?php
namespace Concrete\Core\Job;

/**
 * @since 5.7.5
 */
class JobResult
{
    public $error;
    public $result;
    public $jDateLastRun;
    public $jHandle;
    public $jID;

    public function isError()
    {
        return $this->error !== 0;
    }

    public function getResultCode()
    {
        return $this->error;
    }

    public function getResultMessage()
    {
        return $this->result;
    }

    public function getJobID()
    {
        return $this->jID;
    }

    public function getJobHandle()
    {
        return $this->jHandle;
    }

    public function getDateLastRun()
    {
        return $this->jDateLastRun;
    }
}
