<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;

interface ProcessorInterface
{
    /**
     * Initialize the processor from the configured values.
     *
     * @param \Concrete\Core\Config\Repository\Repository $config
     *
     * @return $this
     */
    public function readConfiguration(Repository $config);
}
