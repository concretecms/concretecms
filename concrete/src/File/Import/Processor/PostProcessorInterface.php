<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;

interface PostProcessorInterface extends ProcessorInterface
{
    /**
     * Get the priority of this post-processor.
     *
     * @return int
     */
    public function getPostProcessPriority();

    /**
     * Check if this post-processor should process an imported file.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $file
     * @param \Concrete\Core\Entity\File\Version $importedVersion
     * @param ImportOptions $options
     *
     * @return bool
     */
    public function shouldPostProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion);

    /**
     * Post-process am imported file.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $file
     * @param \Concrete\Core\Entity\File\Version $importedVersion
     * @param ImportOptions $options
     */
    public function postProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion);
}
