<?php
namespace Concrete\Core\Job;

class Event
{
    protected $j;

    public function __construct(Job $j)
    {
        $this->j = $j;
    }

    public function getJobObject()
    {
        return $this->j;
    }
}
