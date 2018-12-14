<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;

interface ValidatorInterface extends ProcessorInterface
{
    /**
     * Get the priority of this validator.
     *
     * @return int
     */
    public function getValidationPriority();

    /**
     * Check if this validator should validate a file being imported.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $file the file being imported
     * @param \Concrete\Core\File\Import\ImportOptions $options options to b used when importing the file
     *
     * @return bool
     */
    public function shouldValidate(ImportingFile $file, ImportOptions $options);

    /**
     * Validate a file being imported.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $file the file being imported
     * @param \Concrete\Core\File\Import\ImportOptions $options options to b used when importing the file
     *
     * @throws \Concrete\Core\File\Import\ImportException throws an ImportException in case the file is not valid
     */
    public function validate(ImportingFile $file, ImportOptions $options);
}
