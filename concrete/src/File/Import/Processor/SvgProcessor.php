<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Image\Svg\Sanitizer;
use Concrete\Core\File\Image\Svg\SanitizerOptions;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;

class SvgProcessor implements ValidatorInterface, PreProcessorInterface
{
    /**
     * Processor action: do not perform any checks.
     *
     * @var string
     */
    const ACTION_DISABLED = 'disabled';

    /**
     * Processor action: check only that the SVG is a valid XML file.
     *
     * @var string
     */
    const ACTION_CHECKVALIDITY = 'check-validity';
    
    /**
     * Processor action: sanitize the file.
     *
     * @var string
     */
    const ACTION_SANITIZE = 'sanitize';

    /**
     * Processor action: reject the file.
     *
     * @var string
     */
    const ACTION_REJECT = 'reject';

    /**
     * Default processor action.
     *
     * @var string
     */
    const ACTION_DEFAULT = self::ACTION_SANITIZE;

    /**
     * The SVG sanitizer.
     *
     * @var \Concrete\Core\File\Image\Svg\Sanitizer
     */
    protected $sanitizer;

    /**
     * The SVG sanitizer options.
     *
     * @var \Concrete\Core\File\Image\Svg\SanitizerOptions
     */
    protected $sanitizerOptions;

    /**
     * The action that this processor should perform.
     *
     * @var string
     */
    private $action = self::ACTION_SANITIZE;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\Image\Svg\Sanitizer $sanitizer
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions $sanitizerOptions
     */
    public function __construct(Sanitizer $sanitizer, SanitizerOptions $sanitizerOptions)
    {
        $this->sanitizer = $sanitizer;
        $this->sanitizerOptions = $sanitizerOptions;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ProcessorInterface::readConfiguration()
     */
    public function readConfiguration(Repository $config)
    {
        $this->setAction($config->get('concrete.file_manager.images.svg_sanitization.action'));
        $this->sanitizerOptions
            ->setElementAllowlist($config->get('concrete.file_manager.images.svg_sanitization.allowed_tags'))
            ->setAttributeAllowlist($config->get('concrete.file_manager.images.svg_sanitization.allowed_attributes'))
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ValidatorInterface::getValidationPriority()
     */
    public function getValidationPriority()
    {
        return FileExtensionValidator::VALIDATOR_PRIORITY - 10;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ValidatorInterface::shouldValidate()
     */
    public function shouldValidate(ImportingFile $file, ImportOptions $options)
    {
        return $this->getAction() !== static::ACTION_DISABLED && ($file->getFileType()->isSVG() || $file->getMimeType() === "image/svg+xml");
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ValidatorInterface::validate()
     */
    public function validate(ImportingFile $file, ImportOptions $options)
    {
        if (!$this->sanitizer->fileContainsValidXml($file->getLocalFilename())) {
            throw new ImportException(t('The SVG file is malformed.'), ImportException::E_FILE_INVALID);
        }
        switch ($this->getAction()) {
            case static::ACTION_REJECT:
                $nodesToBeRemoved = $this->sanitizer->checkFile($file->getLocalFilename(), $this->sanitizerOptions);
                if (!empty($nodesToBeRemoved)) {
                    throw new ImportException(t('The SVG file contains elements that could be potentially harmful.'), ImportException::E_FILE_INVALID);
                }
                break;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::getPreProcessPriority()
     */
    public function getPreProcessPriority()
    {
        return 1000;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::shouldPreProcess()
     */
    public function shouldPreProcess(ImportingFile $file, ImportOptions $options)
    {
        return $this->getAction() === static::ACTION_SANITIZE && ($file->getFileType()->isSVG() || $file->getMimeType() === "image/svg+xml");
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::preProcess()
     */
    public function preProcess(ImportingFile $file, ImportOptions $options)
    {
        $this->sanitizer->sanitizeFile($file->getLocalFilename(), $this->sanitizerOptions);
    }

    /**
     * Get the action that should be taken.
     *
     * @return string One of the ACTION_... constants
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the action that should be taken.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setAction($value)
    {
        $value = (string) $value;
        switch ($value) {
            case static::ACTION_DISABLED:
            case static::ACTION_CHECKVALIDITY:
            case static::ACTION_SANITIZE:
            case static::ACTION_REJECT:
                $this->action = $value;
                break;
            default:
                $this->action = static::ACTION_DEFAULT;
        }

        return $this;
    }
}
