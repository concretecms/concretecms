<?php

namespace Concrete\Core\Foundation\Queue\Batch\Command;

interface BatchableCommandInterface
{
    /**
     * Get the handle of the batch queue.
     */
    public function getBatchHandle(): string;
}
