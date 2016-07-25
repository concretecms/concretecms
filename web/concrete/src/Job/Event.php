<?php
namespace Concrete\Core\Job;

use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class Event extends AbstractEvent
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
