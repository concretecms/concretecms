<?php

namespace Concrete\Core\Foundation\Queue\Batch\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

interface BatchableCommandInterface extends CommandInterface
{
    /**
     * Get the handle of the batch queue.
     */
    public function getBatchHandle(): string;
}
