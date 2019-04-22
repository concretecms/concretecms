<?php
namespace Concrete\Core\File;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Import\Processor\LegacyPostProcessor;
use Concrete\Core\File\ImportProcessor\ProcessorInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @deprecated use the new Concrete\Core\File\Import\FileImporter class
 * @see \Concrete\Core\File\Import\FileImporter
 */
class Importer
{
    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_FILE_ERROR_DEFAULT
     *
     * @var int
     */
    const E_PHP_FILE_ERROR_DEFAULT = ImportException::E_PHP_FILE_ERROR_DEFAULT;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE
     *
     * @var int
     */
    const E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE = ImportException::E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE
     *
     * @var int
     */
    const E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE = ImportException::E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_FILE_PARTIAL_UPLOAD
     *
     * @var int
     */
    const E_PHP_FILE_PARTIAL_UPLOAD = ImportException::E_PHP_FILE_PARTIAL_UPLOAD;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_NO_FILE
     *
     * @var int
     */
    const E_PHP_NO_FILE = ImportException::E_PHP_NO_FILE;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_NO_TMP_DIR
     *
     * @var int
     */
    const E_PHP_NO_TMP_DIR = ImportException::E_PHP_NO_TMP_DIR;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_CANT_WRITE
     *
     * @var int
     */
    const E_PHP_CANT_WRITE = ImportException::E_PHP_CANT_WRITE;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_PHP_EXTENSION
     *
     * @var int
     */
    const E_PHP_EXTENSION = ImportException::E_PHP_EXTENSION;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_FILE_INVALID_EXTENSION
     *
     * @var int
     */
    const E_FILE_INVALID_EXTENSION = ImportException::E_FILE_INVALID_EXTENSION;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_FILE_INVALID
     *
     * @var int
     */
    const E_FILE_INVALID = ImportException::E_FILE_INVALID;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_FILE_UNABLE_TO_STORE
     *
     * @var int
     */
    const E_FILE_UNABLE_TO_STORE = ImportException::E_FILE_UNABLE_TO_STORE;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_FILE_INVALID_STORAGE_LOCATION
     *
     * @var int
     */
    const E_FILE_INVALID_STORAGE_LOCATION = ImportException::E_FILE_INVALID_STORAGE_LOCATION;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED
     *
     * @var int
     */
    const E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED = ImportException::E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE
     *
     * @var int
     */
    const E_FILE_EXCEEDS_POST_MAX_FILE_SIZE = ImportException::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportOptions::isSkipThumbnailGeneration()
     * @see \Concrete\Core\File\Import\ImportOptions::setSkipThumbnailGeneration()
     *
     * @var bool
     */
    protected $rescanThumbnailsOnImport = true;

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ProcessorManager
     */
    protected $importProcessors = [];

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\File\Import\FileImporter
     * @var int
     */
    private $fileImporter;

    /**
     * @var \Concrete\Core\File\Import\ProcessorManager
     */
    private $deprecatedProcessorUsed = [];

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
        $this->fileImporter = $this->app->make(FileImporter::class);
    }

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportException::describeErrorCode()
     *
     * @param int $code
     *
     * @return string
     */
    public static function getErrorMessage($code)
    {
        return ImportException::describeErrorCode($code);
    }

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ProcessorManager
     *
     * @param \Concrete\Core\File\ImportProcessor\ProcessorInterface $processor
     */
    public function addImportProcessor(ProcessorInterface $processor)
    {
        $this->importProcessors[] = $processor;
    }

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\FileImporter::generatePrefix()
     *
     * @return string
     */
    public function generatePrefix()
    {
        return $this->fileImporter->generatePrefix();
    }

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\FileImporter::importLocalFile()
     *
     * @param string $pointer
     * @param string|bool $filename
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Tree\Node\Type\FileFolder|null|false $fr
     * @param string|null $prefix
     *
     * @return \Concrete\Core\Entity\File\Version|int
     */
    public function import($pointer, $filename = false, $fr = false, $prefix = null)
    {
        $options = $this->buildOptions($fr, $prefix);
        $this->useDeprecatedProcessors();
        try {
            return $this->fileImporter->importLocalFile($pointer, (string) $filename, $options);
        } catch (ImportException $x) {
            return $x->getCode();
        }
    }

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\FileImporter::importFromIncoming()
     *
     * @param string $filename
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Tree\Node\Type\FileFolder|null|false $fr
     *
     * @return \Concrete\Core\Entity\File\Version|int
     */
    public function importIncomingFile($filename, $fr = false)
    {
        $options = $this->buildOptions($fr);
        $this->useDeprecatedProcessors();
        try {
            return $this->fileImporter->importFromIncoming($filename, '', $options);
        } catch (ImportException $x) {
            return $x->getCode();
        }
    }

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\FileImporter::importUploadedFile()
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Tree\Node\Type\FileFolder|null|false $fr
     *
     * @return \Concrete\Core\Entity\File\Version|int
     */
    public function importUploadedFile(UploadedFile $uploadedFile = null, $fr = false)
    {
        $options = $this->buildOptions($fr)->setCanChangeLocalFile(true);
        $this->useDeprecatedProcessors();
        try {
            return $this->fileImporter->importUploadedFile($uploadedFile, '', $options);
        } catch (ImportException $x) {
            return $x->getCode();
        }
    }

    /**
     * @deprecated
     * @see \Concrete\Core\File\Import\ImportOptions::isSkipThumbnailGeneration()
     * @see \Concrete\Core\File\Import\ImportOptions::setSkipThumbnailGeneration()
     *
     * @param bool $value
     */
    public function setRescanThumbnailsOnImport($value)
    {
        $this->rescanThumbnailsOnImport = $value;
    }

    /**
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Tree\Node\Type\FileFolder|null|false $fr
     * @param string|null $customPrefix
     *
     * @return \Concrete\Core\File\Import\ImportOptions
     */
    private function buildOptions($fr, $customPrefix = '')
    {
        $options = $this->app->make(ImportOptions::class);
        if ($fr instanceof FileEntity) {
            $options->setAddNewVersionTo($fr);
        } elseif ($fr instanceof FileFolder) {
            $options->setImportToFolder($fr);
        }

        return $options
            ->setSkipThumbnailGeneration(!$this->rescanThumbnailsOnImport)
            ->setCustomPrefix($customPrefix)
        ;
    }

    private function useDeprecatedProcessors()
    {
        $configuredProcessors = [];
        foreach ($this->importProcessors as $processor) {
            $configuredProcessors[spl_object_hash($processor)] = $processor;
        }
        $hashesOfAddedProcessors = array_diff(array_keys($configuredProcessors), array_keys($this->deprecatedProcessorUsed));
        $hashesOfRemovedProcessors = array_diff(array_keys($this->deprecatedProcessorUsed), array_keys($configuredProcessors));
        foreach ($hashesOfAddedProcessors as $hash) {
            $wrapper = $this->app->make(LegacyPostProcessor::class, ['implementation' => $configuredProcessors[$hash]]);
            $this->fileImporter->getProcessorManager()->registerProcessor($wrapper);
            $this->deprecatedProcessorUsed[$hash] = $wrapper;
        }
        foreach ($hashesOfRemovedProcessors as $hash) {
            $wrapper = $this->deprecatedProcessorUsed[$hash];
            $this->fileImporter->getProcessorManager()->unregisterProcessor($wrapper);
            unset($this->deprecatedProcessorUsed[$hash]);
        }
    }
}
