<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;

interface PreProcessorInterface extends ProcessorInterface
{
    /**
     * Get the priority of this pre-processor.
     *
     * @return int
     */
    public function getPreProcessPriority();

    /**
     * Check if this pre-processor should process a file being imported.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $file the file being imported
     * @param \Concrete\Core\File\Import\ImportOptions $options options to b used when importing the file
     *
     * @return bool
     */
    public function shouldPreProcess(ImportingFile $file, ImportOptions $options);

    /**
     * Pre-process a file being imported.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $file the file being imported
     * @param \Concrete\Core\File\Import\ImportOptions $options options to b used when importing the file
     */
    public function preProcess(ImportingFile $file, ImportOptions $options);
}
