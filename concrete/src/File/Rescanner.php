<?php

namespace Concrete\Core\File;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Import\ProcessorManager;
use Concrete\Core\File\Service\VolatileDirectory;

class Rescanner
{
    /**
     * @var \Concrete\Core\File\Import\ProcessorManager
     */
    protected $processorManager;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct(ProcessorManager $processorManager, Application $app)
    {
        $this->processorManager = $processorManager;
        $this->app = $app;
    }

    /**
     * Rescan the currently approved version of a file.
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Concrete\Core\Entity\File\Version The processed file version (it's the currently approved version, or a new version created if needed)
     */
    public function rescanFile(File $file): Version
    {
        $fileVersion = $file->getApprovedVersion();

        return $this->rescanFileVersion($fileVersion);
    }

    /**
     * Rescan a specific version of a file.
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Concrete\Core\Entity\File\Version The processed file version (may be $fileVersion or a a newly created version)
     */
    public function rescanFileVersion(Version $fileVersion): Version
    {
        $fileVersion = $this->applyPreProcessors($fileVersion);
        $this->refreshAttributes($fileVersion);
        $this->rescanThumbnails($fileVersion);
        $fileVersion->releaseImagineImage();

        return $fileVersion;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function refreshAttributes(Version $fileVersion): void
    {
        $errorCode = $fileVersion->refreshAttributes(false);
        if ($errorCode !== null) {
            throw new UserMessageException(ImportException::describeErrorCode($errorCode));
        }
    }

    protected function applyPreProcessors(Version $fileVersion): Version
    {
        $volatileDirectory = $this->app->make(VolatileDirectory::class);
        $file = $this->createImportingFile($volatileDirectory, $fileVersion);
        $options = $this->app->make(ImportOptions::class)
            ->setAddNewVersionTo($fileVersion->getFile())
            ->setCanChangeLocalFile(true)
            ->setSkipThumbnailGeneration(true)
        ;
        $sha1 = sha1_file($file->getLocalFilename());
        foreach ($this->processorManager->getPreProcessors() as $processor) {
            if ($processor->shouldPreProcess($file, $options)) {
                $processor->preProcess($file, $options);
            }
        }
        $changed = $sha1 !== sha1_file($file->getLocalFilename());
        if ($changed) {
            $fileVersion = $fileVersion->getFile()->createNewVersion(false);
            $fileVersion->updateContents(file_get_contents($file->getLocalFilename()));
        }

        return $fileVersion;
    }

    protected function createImportingFile(VolatileDirectory $volatileDirectory, Version $fileVersion): ImportingFile
    {
        $localFilename = tempnam($volatileDirectory->getPath(), 'rescan');
        $writeHandle = fopen($localFilename, 'w');
        try {
            $readHandle = $fileVersion->getFileResource()->readStream();
            try {
                stream_copy_to_stream($readHandle, $writeHandle);
            } finally {
                fclose($readHandle);
            }
        } finally {
            fclose($writeHandle);
        }

        return $this->app->make(ImportingFile::class, ['localFilename' => $localFilename, 'concreteFilename' => $fileVersion->getFileName()]);
    }

    protected function rescanThumbnails(Version $fileVersion): void
    {
        $fileVersion->refreshThumbnails(true);
    }
}
