<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Foundation\Command\Command;

class DeleteScheduledTaskCommand extends Command
{

    /**
     * @var string
     */
    protected $scheduledTaskId;

    public function __construct(string $scheduledTaskId)
    {
        $this->scheduledTaskId = $scheduledTaskId;
    }

    /**
     * @return string
     */
    public function getScheduledTaskId(): string
    {
        return $this->scheduledTaskId;
    }



}