<?php

namespace Concrete\Core\Command\Process\Event;

use Concrete\Core\Entity\Command\Process;

class ProcessEvent
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }



}
