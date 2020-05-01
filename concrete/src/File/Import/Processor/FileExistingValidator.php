<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\ValidationService;

class FileExistingValidator implements ValidatorInterface
{
    const VALIDATOR_PRIORITY = 0x3FFFFFFF;

    /**
     * @var \Concrete\Core\File\ValidationService
     */
    protected $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ProcessorInterface::readConfiguration()
     */
    public function readConfiguration(Repository $config)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ValidatorInterface::getValidationPriority()
     */
    public function getValidationPriority()
    {
        return static::VALIDATOR_PRIORITY;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ValidatorInterface::shouldValidate()
     */
    public function shouldValidate(ImportingFile $file, ImportOptions $options)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ValidatorInterface::validate()
     */
    public function validate(ImportingFile $file, ImportOptions $options)
    {
        if (!$this->validationService->file($file->getLocalFilename())) {
            throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
        }
    }
}
