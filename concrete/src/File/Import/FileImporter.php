<?php

namespace Concrete\Core\File\Import;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\File\Version as FileVersionEntity;
use Concrete\Core\File\File;
use Concrete\Core\File\Incoming;
use Concrete\Core\File\Service\Application as ApplicationFileService;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Exception;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

/**
 * A class to be used to import files into the application file manager.
 */
class FileImporter
{
    /**
     * The container instance to be used to build dependencies.
     *
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\File\Import\ProcessorManager
     */
    private $processorManager;

    /**
     * @var \Concrete\Core\File\Service\Application
     */
    private $applicationFileService;

    /**
     * @var \Concrete\Core\File\Incoming
     */
    private $incoming;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\File\Import\ProcessorManager $processorManager
     * @param \Concrete\Core\File\Service\Application $applicationFileService
     * @param Incoming $incoming
     */
    public function __construct(Application $app, ProcessorManager $processorManager, ApplicationFileService $applicationFileService, Incoming $incoming)
    {
        $this->app = $app;
        $this->processorManager = $processorManager;
        $this->applicationFileService = $applicationFileService;
        $this->incoming = $incoming;
    }

    /**
     * Get the processor list manager.
     *
     * @return \Concrete\Core\File\Import\ProcessorManager
     */
    public function getProcessorManager()
    {
        return $this->processorManager;
    }

    /**
     * Import a local file into the application file manager.
     *
     * @param string $localFilename The path to the file
     * @param string $concreteFilename A custom name to give to the file (if empty we'll derive it from $localFilename)
     * @param \Concrete\Core\File\Import\ImportOptions|null $options the options to be used to import the file
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return \Concrete\Core\Entity\File\Version
     */
    public function importLocalFile($localFilename, $concreteFilename = '', ImportOptions $options = null)
    {
        if ($options === null) {
            $options = $this->app->make(ImportOptions::class);
        }
        if ($options->canChangeLocalFile() === false && is_file($localFilename)) {
            $vd = $this->app->make(VolatileDirectory::class);
            $tempFilename = $vd->getPath() . '/file';
            copy($localFilename, $tempFilename);
            if (!$concreteFilename) {
                $concreteFilename = $localFilename;
            }
            $localFilename = $tempFilename;
        }
        $importingFile = $this->app->make(ImportingFile::class, ['localFilename' => $localFilename, 'concreteFilename' => $concreteFilename]);

        return $this->import($importingFile, $options);
    }

    /**
     * Import a file from the incoming directory into the application file manager.
     *
     * @param string $incomingFilename The path to the file
     * @param string $concreteFilename A custom name to give to the file (if empty we'll derive it from $incomingFilename)
     * @param \Concrete\Core\File\Import\ImportOptions|null $options the options to be used to import the file
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return \Concrete\Core\Entity\File\Version
     */
    public function importFromIncoming($incomingFilename, $concreteFilename = '', ImportOptions $options = null)
    {
        $localFilename = $this->resolveIncomingFilename($incomingFilename, $copiedLocally, $volatileDirectory);
        if ($copiedLocally) {
            $options = $options ? clone $options : $this->app->make(ImportOptions::class);
            $options->setCanChangeLocalFile(true);
        }

        return $this->importLocalFile($localFilename, $concreteFilename ?: $incomingFilename, $options);
    }

    /**
     * Import a file received via a POST request into the application file manager.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile The uploaded file
     * @param string $concreteFilename A custom name to give to the file (if empty we'll derive it from $uploadedFile)
     * @param \Concrete\Core\File\Import\ImportOptions|null $options the options to be used to import the file
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return \Concrete\Core\Entity\File\Version
     *
     * @example
     * <pre><code>
     * $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
     * $request = $app->make(\Concrete\Core\Http\Request::class);
     * $importer = $app->make(\Concrete\Core\File\Import\FileImporter::class);
     * try {
     *     $fv = $importer->importUploadedFile($request->files->get('field_name'));
     * } catch (\Concrete\Core\File\Import\ImportException $x) {
     *     // Manage the import exception
     * }
     * </code></pre>
     */
    public function importUploadedFile(UploadedFile $uploadedFile = null, $concreteFilename = '', ImportOptions $options = null)
    {
        if ($uploadedFile === null) {
            throw ImportException::fromErrorCode(ImportException::E_PHP_NO_FILE);
        }
        if (!$uploadedFile->isValid()) {
            throw ImportException::fromErrorCode($uploadedFile->getError());
        }
        $options = $options ? clone $options : $this->app->make(ImportOptions::class);
        $options->setCanChangeLocalFile(true);

        return $this->importLocalFile($uploadedFile->getPathname(), $concreteFilename ?: $uploadedFile->getClientOriginalName(), $options);
    }

    /**
     * Generate a file prefix.
     *
     * @return string
     */
    public function generatePrefix()
    {
        $prefix = mt_rand(10, 99) . time();

        return $prefix;
    }

    /**
     * Import a file into the application file manager.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $importingFile the file being imported
     * @param \Concrete\Core\File\Import\ImportOptions $options the options to be used to import the file
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return \Concrete\Core\Entity\File\Version
     */
    protected function import(ImportingFile $importingFile, ImportOptions $options)
    {
        $this->applyValidators($importingFile, $options);
        $this->applyPreProcessors($importingFile, $options);
        $importingFile->releaseImage();
        $fileVersion = $this->save($importingFile, $options);
        $this->applyPostProcessors($importingFile, $options, $fileVersion);
        $fileVersion->releaseImagineImage();

        /** @var LoggerFactory $loggerFactory */
        $loggerFactory = $this->app->make(LoggerFactory::class);
        $logger = $loggerFactory->createLogger(Channels::CHANNEL_FILES);

        try {
            $logger->notice(t("File %s successfully imported.", $fileVersion->getFileName()));
        } catch (Exception $err) {
            // Skip any errors while logging to pass the automated tests
        }

        return $fileVersion;
    }

    /**
     * Apply the validators to the file being imported.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $importingFile
     * @param \Concrete\Core\File\Import\ImportOptions $options
     *
     * @throws \Concrete\Core\File\Import\ImportException
     */
    protected function applyValidators(ImportingFile $importingFile, ImportOptions $options)
    {
        foreach ($this->getProcessorManager()->getValidators() as $validator) {
            if ($validator->shouldValidate($importingFile, $options)) {
                $validator->validate($importingFile, $options);
            }
        }
    }

    /**
     * Apply the pre-processors to the file being imported.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $importingFile
     * @param \Concrete\Core\File\Import\ImportOptions $options
     */
    protected function applyPreProcessors(ImportingFile $importingFile, ImportOptions $options)
    {
        foreach ($this->getProcessorManager()->getPreProcessors() as $preProcessor) {
            if ($preProcessor->shouldPreProcess($importingFile, $options)) {
                $preProcessor->preProcess($importingFile, $options);
            }
        }
    }

    /**
     * Actually import a file into the application file manager.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $importingFile the file being imported
     * @param \Concrete\Core\File\Import\ImportOptions $options the options to be used to import the file
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return \Concrete\Core\Entity\File\Version
     */
    protected function save(ImportingFile $importingFile, ImportOptions $options)
    {
        $prefix = $options->getCustomPrefix();
        if ($prefix === '') {
            $prefixIsAutoGenerated = true;
            $prefix = $this->generatePrefix();
        } else {
            $prefixIsAutoGenerated = false;
        }
        $storageLocation = $options->getStorageLocation();
        $filesystem = $storageLocation->getFileSystemObject();

        $src = @fopen($importingFile->getLocalFilename(), 'rb');
        if ($src === false) {
            throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
        }
        try {
            $filesystem->writeStream(
                $this->applicationFileService->prefix($prefix, $importingFile->getConcreteFilenameSanitized()),
                $src,
                [
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                    'mimetype' => $importingFile->getMimeType(),
                ]
            );
            $writeError = null;
        } catch (Exception $e) {
            $writeError = $e;
        } catch (Throwable $e) {
            $writeError = $e;
        }
        @fclose($src);
        if ($writeError !== null) {
            throw ImportException::fromErrorCode($prefixIsAutoGenerated ? ImportException::E_FILE_UNABLE_TO_STORE : ImportException::E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED, $writeError);
        }

        $file = $options->getAddNewVersionTo();
        if ($file !== null) {
            // We get a new version to modify
            $fileVersion = $file->getVersionToModify(true);
            $fileVersion->updateFile(
                $importingFile->getConcreteFilenameSanitized(),
                $prefix
            );
        } else {
            // We create a new File instance
            $fileVersion = File::add(
                $importingFile->getConcreteFilenameSanitized(),
                $prefix,
                ['fvTitle' => $importingFile->getConcreteFilename()],
                $storageLocation,
                $options->getImportToFolder()
            );
        }
        $fileVersion->refreshAttributes(false);

        return $fileVersion;
    }

    /**
     * Apply the post-processors to the imported file.
     *
     * @param \Concrete\Core\File\Import\ImportingFile $importingFile
     * @param \Concrete\Core\File\Import\ImportOptions $options
     * @param \Concrete\Core\Entity\File\Version $fileVersion
     *
     * @throws \Concrete\Core\File\Import\ImportException
     */
    protected function applyPostProcessors(ImportingFile $importingFile, ImportOptions $options, FileVersionEntity $fileVersion)
    {
        foreach ($this->getProcessorManager()->getPostProcessors() as $postProcessor) {
            if ($postProcessor->shouldPostProcess($importingFile, $options, $fileVersion)) {
                $postProcessor->postProcess($importingFile, $options, $fileVersion);
            }
        }
    }

    /**
     * Get the local path of an incoming file (if the incoming file system is not local, we'll create a copy of it in a volatile directory).
     *
     * @param string $incomingFilename The file name in the incoming file system
     * @param bool $copiedLocally this output parameter will be set to true if we had to copy the file locally
     * @param \Concrete\Core\File\Service\VolatileDirectory|null $volatileDirectory this output parameter will be set to a VolatileDirectory instance if we had to copy the file locally
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return string
     */
    protected function resolveIncomingFilename($incomingFilename, &$copiedLocally, VolatileDirectory &$volatileDirectory = null)
    {
        $copiedLocally = false;
        $incoming = $this->app->make(Incoming::class);
        $incomingStorageLocation = $incoming->getIncomingStorageLocation();
        $incomingFilesystem = $incomingStorageLocation->getFileSystemObject();
        $incomingPath = $incoming->getIncomingPath();
        if (!$incomingFilesystem->has($incomingPath . '/' . $incomingFilename)) {
            throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
        }
        $incomingAdapter = $incomingFilesystem->getAdapter();
        if ($incomingAdapter instanceof AbstractAdapter) {
            $localPath = $incomingAdapter->applyPathPrefix($incomingPath . '/' . $incomingFilename);
            if (is_file($localPath)) {
                return $localPath;
            }
        }
        $fromStream = null;
        $toStream = null;
        try {
            $volatileDirectory = $this->app->make(VolatileDirectory::class);
            $localPath = $volatileDirectory->getPath() . '/file';
            try {
                $fromStream = $incomingFilesystem->readStream($incomingPath . '/' . $incomingFilename);
            } catch (Exception $x) {
                $fromStream = false;
            }
            if ($fromStream === false) {
                throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
            }
            $toStream = @fopen($localPath);
            if ($toStream === false) {
                throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
            }
            if (@stream_copy_to_stream($fromStream, $toStream) === false) {
                throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID);
            }
            $copiedLocally = true;

            return $localPath;
        } finally {
            if ($toStream) {
                @fclose($toStream);
            }
            if ($fromStream) {
                @fclose($fromStream);
            }
        }
    }
}
