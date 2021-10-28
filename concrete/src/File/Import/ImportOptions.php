<?php

namespace Concrete\Core\File\Import;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Tree\Node\Type\FileFolder;

/**
 * Options to be used when importing a file.
 */
class ImportOptions
{
    /**
     * @var \Concrete\Core\File\StorageLocation\StorageLocationFactory
     */
    protected $storageLocationFactory;

    /**
     * @var \Concrete\Core\File\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Concrete\Core\Tree\Node\Type\FileFolder|null
     */
    private $importToFolder;

    /**
     * @var \Concrete\Core\Entity\File\File|null
     */
    private $addNewFileVersion;

    /**
     * @var \Concrete\Core\Entity\File\StorageLocation\StorageLocation|null
     */
    private $storageLocation;

    /**
     * @var string
     */
    private $customPrefix = '';

    /**
     * Skip the thumbnail generation?
     *
     * @var bool
     */
    private $skipThumbnailGeneration = false;

    /**
     * Can the local file be changed by file processors? If no, we'll create a copy of the file being imported.
     *
     * @var bool
     */
    private $canChangeLocalFile = false;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\Filesystem $filesystem
     * @param \Concrete\Core\File\StorageLocation\StorageLocationFactory $storageLocationFactory
     */
    public function __construct(Filesystem $filesystem, StorageLocationFactory $storageLocationFactory)
    {
        $this->filesystem = $filesystem;
        $this->storageLocationFactory = $storageLocationFactory;
    }

    /**
     * The import process should create new files in a specific folder.
     *
     * @param \Concrete\Core\Tree\Node\Type\FileFolder $folder
     *
     * @return $this
     */
    public function setImportToFolder(FileFolder $folder)
    {
        $this->storageLocation = null;
        $this->addNewFileVersion = null;
        $this->importToFolder = $folder;

        return $this;
    }

    /**
     * The import process should add a new Version to an existing File.
     *
     * @param \Concrete\Core\Entity\File\File $toFile
     *
     * @return $this
     */
    public function setAddNewVersionTo(FileEntity $toFile)
    {
        $this->storageLocation = null;
        $this->importToFolder = null;
        $this->addNewFileVersion = $toFile;

        return $this;
    }

    /**
     * The import process should store the file in this folder.
     *
     * @throws \Concrete\Core\File\Import\ImportException if the root folder does not exist
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder
     */
    public function getImportToFolder()
    {
        if ($this->importToFolder === null) {
            $file = $this->getAddNewVersionTo();
            if ($file !== null) {
                $this->importToFolder = $file->getFileFolderObject();
            }
            if ($this->importToFolder === null) {
                $this->importToFolder = $this->filesystem->getRootFolder();
                if ($this->importToFolder === null) {
                    throw ImportException::fromErrorCode(ImportException::E_FILE_MISSING_ROOT_FOLDER);
                }
            }
        }

        return $this->importToFolder;
    }

    /**
     * The import process should add a new file Version to this File object (NULL: no).
     *
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getAddNewVersionTo()
    {
        return $this->addNewFileVersion;
    }

    /**
     * Get the storage location where imported files should be saved to.
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    public function getStorageLocation()
    {
        if ($this->storageLocation === null) {
            $file = $this->getAddNewVersionTo();
            if ($file !== null) {
                $this->storageLocation = $this->getFileStorageLocation($file);
            } else {
                $this->storageLocation = $this->getFolderStorageLocation($this->getImportToFolder());
            }
        }

        return $this->storageLocation;
    }

    /**
     * Set the custom prefix to be used to store the file.
     * Make sure it conforms to standards (e.g. it is 12 digits, numeric only).
     *
     * @param string $value
     *
     * @return $this
     */
    public function setCustomPrefix($value)
    {
        $this->customPrefix = (string) $value;

        return $this;
    }

    /**
     * Get the custom prefix to be used to store the file.
     *
     * @return string
     */
    public function getCustomPrefix()
    {
        return $this->customPrefix;
    }

    /**
     * Skip the thumbnail generation?
     *
     * @return bool
     */
    public function isSkipThumbnailGeneration()
    {
        return $this->skipThumbnailGeneration;
    }

    /**
     * Skip the thumbnail generation?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setSkipThumbnailGeneration($value)
    {
        $this->skipThumbnailGeneration = (bool) $value;

        return $this;
    }

    /**
     * Can the local file be changed by file processors? If no, we'll create a copy of the file being imported.
     *
     * @return bool
     */
    public function canChangeLocalFile()
    {
        return $this->canChangeLocalFile;
    }

    /**
     * Can the local file be changed by file processors? If no, we'll create a copy of the file being imported.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setCanChangeLocalFile($value)
    {
        $this->canChangeLocalFile = (bool) $value;

        return $this;
    }

    /**
     * Get the storage location associated to a file.
     *
     * @param \Concrete\Core\Entity\File\File $file
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    protected function getFileStorageLocation(FileEntity $file)
    {
        $storageLocation = $file->getFileStorageLocationObject();

        if ($storageLocation === null) {
            throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID_STORAGE_LOCATION);
        }

        return $storageLocation;
    }

    /**
     * Get the storage location associated to a folder.
     *
     * @param \Concrete\Core\Tree\Node\Type\FileFolder $folder
     *
     * @throws \Concrete\Core\File\Import\ImportException
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    protected function getFolderStorageLocation(FileFolder $folder)
    {
        $storageLocation = $folder->getTreeNodeStorageLocationObject();

        if ($storageLocation === null) {
            $storageLocation = $this->storageLocationFactory->fetchDefault();
        }

        if ($storageLocation === null) {
            throw ImportException::fromErrorCode(ImportException::E_FILE_INVALID_STORAGE_LOCATION);
        }

        return $storageLocation;
    }
}
