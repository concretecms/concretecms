<?php

namespace Concrete\Core\Foundation\Queue\Batch;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Concrete\Core\Foundation\Queue\Batch\Response\BatchProcessorResponse;

interface BatchProcessFactoryInterface
{

    /**
     * @param $mixed
     * @return BatchableCommandInterface[]
     */
    public function getCommands($mixed) : array;

    /**
     * @return string
     */
    public function getBatchHandle();

}