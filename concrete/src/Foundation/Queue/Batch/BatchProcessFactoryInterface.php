<?php

namespace Concrete\Core\Foundation\Queue\Batch;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Concrete\Core\Foundation\Queue\Batch\Response\BatchProcessorResponse;

interface BatchProcessFactoryInterface
{

    public function getCommand($mixed) : BatchableCommandInterface;
    public function getBatchHandle();

}