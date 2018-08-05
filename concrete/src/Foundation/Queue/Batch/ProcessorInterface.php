<?php

namespace Concrete\Core\Foundation\Queue\Batch;


use Concrete\Core\Foundation\Queue\Batch\Response\BatchProcessorResponse;

interface ProcessorInterface
{

    public function process(BatchProcessFactoryInterface $factory, array $items) : BatchProcessorResponse;

}