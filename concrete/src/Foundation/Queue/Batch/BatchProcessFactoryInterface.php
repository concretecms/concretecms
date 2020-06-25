<?php

namespace Concrete\Core\Foundation\Queue\Batch;

interface BatchProcessFactoryInterface
{
    /**
     * Get the handle identifying the batch process.
     */
    public function getBatchHandle(): string;

    /**
     * Build the list of the batch commands.
     *
     * @param mixed $mixed the data to be used to build the commands
     *
     * @return \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface[]
     */
    public function getCommands($mixed): array;
}
