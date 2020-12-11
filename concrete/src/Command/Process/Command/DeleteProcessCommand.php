<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Foundation\Command\Command;

class DeleteProcessCommand extends Command
{

    /**
     * @var string
     */
    protected $processId;

    /**
     * DeleteProcessCommand constructor.
     * @param string $processId
     */
    public function __construct(string $processId)
    {
        $this->processId = $processId;
    }

    /**
     * @return string
     */
    public function getProcessId(): string
    {
        return $this->processId;
    }




}