<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Document\Xml\Sanitizer;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;

/**
 * Validates/sanitizes uploaded XML (and XSLT) documents that are not handled by
 * a more specific processor (eg SVG).
 *
 * Plain XML files can carry a <?xml-stylesheet?> processing instruction that
 * points to an (attacker-controlled) XSLT stylesheet. When such a file is
 * opened directly in a browser, the browser will fetch the referenced
 * stylesheet and use it to transform the document into HTML, potentially
 * executing attacker-controlled JavaScript in the origin serving the file.
 * This processor strips (or rejects) that kind of active content.
 */
class XmlProcessor implements ValidatorInterface, PreProcessorInterface
{
    /**
     * Processor action: do not perform any checks.
     *
     * @var string
     */
    const ACTION_DISABLED = 'disabled';

    /**
     * Processor action: check only that the file is a valid XML file.
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
    const ACTION_DEFAULT = self::ACTION_REJECT;

    /**
     * The extensions handled by this processor.
     *
     * @var string[]
     */
    const HANDLED_EXTENSIONS = ['xml', 'xsl', 'xslt'];

    /**
     * The mime types handled by this processor.
     *
     * @var string[]
     */
    const HANDLED_MIMETYPES = ['text/xml', 'application/xml', 'text/xsl', 'application/xslt+xml'];

    /**
     * The XML sanitizer.
     *
     * @var \Concrete\Core\File\Document\Xml\Sanitizer
     */
    protected $sanitizer;

    /**
     * The action that this processor should perform.
     *
     * @var string
     */
    private $action = self::ACTION_SANITIZE;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\Document\Xml\Sanitizer $sanitizer
     */
    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ProcessorInterface::readConfiguration()
     */
    public function readConfiguration(Repository $config)
    {
        $this->setAction($config->get('concrete.file_manager.documents.xml_sanitization.action'));

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
        return $this->getAction() !== static::ACTION_DISABLED && $this->isXmlDocument($file);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ValidatorInterface::validate()
     */
    public function validate(ImportingFile $file, ImportOptions $options)
    {
        // A document we can't parse is a document we can't sanitize, so we refuse it
        // whatever the action is: letting it through would store verbatim the very
        // contents that this processor exists to remove.
        if (!$this->sanitizer->fileContainsValidXml($file->getLocalFilename())) {
            throw ImportException::fromErrorCode(ImportException::E_FILE_MALFORMED_XML);
        }
        if ($this->getAction() === static::ACTION_REJECT) {
            $removedNodes = $this->sanitizer->checkFile($file->getLocalFilename());
            if (!empty($removedNodes)) {
                throw ImportException::fromErrorCode(ImportException::E_FILE_HARMFUL_CONTENTS);
            }
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
        return $this->getAction() === static::ACTION_SANITIZE && $this->isXmlDocument($file);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::preProcess()
     */
    public function preProcess(ImportingFile $file, ImportOptions $options)
    {
        $this->sanitizer->sanitizeFile($file->getLocalFilename());
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

    /**
     * Determine whether the file being imported should be treated as a plain XML/XSLT document.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $file
     *
     * @return bool
     */
    protected function isXmlDocument(ImportingFile $file)
    {
        if (in_array($file->getFileExtension(), static::HANDLED_EXTENSIONS, true)) {
            return true;
        }

        return in_array(strtolower((string) $file->getMimeType()), static::HANDLED_MIMETYPES, true);
    }
}