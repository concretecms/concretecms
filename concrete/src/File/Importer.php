<?php
namespace Concrete\Core\File;

use Concrete\Core\File\ImportProcessor\ConstrainImageProcessor;
use Concrete\Core\File\ImportProcessor\ProcessorInterface;
use Concrete\Core\File\ImportProcessor\SetJPEGQualityProcessor;
use Concrete\Core\File\ImportProcessor\AutorotateImageProcessor;
use Concrete\Core\File\StorageLocation\StorageLocation;
use League\Flysystem\AdapterInterface;
use Loader;
use Core;
use Config;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Tree\Node\Type\FileFolder;

class Importer
{
    /**
     * PHP error constants - these match those founds in $_FILES[$field]['error] if it exists.
     */
    const E_PHP_FILE_ERROR_DEFAULT = 0;
    const E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE = 1;
    const E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE = 2;
    const E_PHP_FILE_PARTIAL_UPLOAD = 3;
    const E_PHP_NO_FILE = 4;

    /**
     * concrete5 internal error constants.
     */
    const E_FILE_INVALID_EXTENSION = 10;
    const E_FILE_INVALID = 11; // pointer is invalid file, is a directory, etc...
    const E_FILE_UNABLE_TO_STORE = 12;
    const E_FILE_INVALID_STORAGE_LOCATION = 13;
    const E_FILE_EXCEEDS_POST_MAX_FILE_SIZE = 20;

    protected $rescanThumbnailsOnImport = true;

    protected $importProcessors = array();

    public function __construct()
    {
        $resize = Config::get('concrete.file_manager.restrict_uploaded_image_sizes');
        if ($resize) {
            $width = (int) Config::get('concrete.file_manager.restrict_max_width');
            $height = (int) Config::get('concrete.file_manager.restrict_max_height');
            $quality = (int) Config::get('concrete.file_manager.restrict_resize_quality');
            $resizeProcessor = new ConstrainImageProcessor($width, $height);
            $qualityProcessor = new SetJPEGQualityProcessor($quality);
            $this->addImportProcessor($resizeProcessor);
            $this->addImportProcessor($qualityProcessor);
        }
        
        if (Config::get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
            $this->addImportProcessor(new AutorotateImageProcessor);
        }
    }

    /**
     * Returns a text string explaining the error that was passed.
     *
     * @param int $code
     *
     * @return string
     */
    public function getErrorMessage($code)
    {
        $defaultStorage = StorageLocation::getDefault()->getName();
        $msg = '';
        switch ($code) {
            case self::E_PHP_NO_FILE:
            case self::E_FILE_INVALID:
                $msg = t('Invalid file.');
                break;
            case self::E_FILE_INVALID_EXTENSION:
                $msg = t('Invalid file extension.');
                break;
            case self::E_PHP_FILE_PARTIAL_UPLOAD:
                $msg = t('The file was only partially uploaded.');
                break;
            case self::E_FILE_INVALID_STORAGE_LOCATION:
                $msg = t('No default file storage location could be found to store this file.');
                break;
            case self::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE:
                $msg = t('Uploaded file is too large. The current value of post_max_filesize is %s',
                    ini_get('post_max_size'));
                break;
            case self::E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE:
            case self::E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE:
                $msg = t('Uploaded file is too large. The current value of upload_max_filesize is %s',
                    ini_get('upload_max_filesize'));
                break;
            case self::E_FILE_UNABLE_TO_STORE:
                $msg = t('Unable to copy file to storage location "%s". Please check the settings for the storage location.',
                    $defaultStorage);
                break;
            case self::E_PHP_FILE_ERROR_DEFAULT:
            default:
                $msg = t("An unknown error occurred while uploading the file. Please check that file uploads are enabled, and that your file does not exceed the size of the post_max_size or upload_max_filesize variables.\n\nFile Uploads: %s\nMax Upload File Size: %s\nPost Max Size: %s",
                    ini_get('file_uploads'), ini_get('upload_max_filesize'), ini_get('post_max_size'));
                break;
        }

        return $msg;
    }

    public function addImportProcessor(ProcessorInterface $processor)
    {
        $this->importProcessors[] = $processor;
    }

    /**
     * @return string
     */
    public function generatePrefix()
    {
        $prefix = rand(10, 99) . time();

        return $prefix;
    }

    /**
     * Imports a local file into the system. The file must be added to this path
     * somehow. That's what happens in tools/files/importers/.
     * If a $fr (FileRecord) object is passed, we assign the newly imported FileVersion
     * object to that File. If not, we make a new filerecord.
     *
     * @param string $pointer path to file
     * @param string|bool $filename
     * @param File|FileFolder|bool $fr
     *
     * @return number Error Code | \Concrete\Core\EntiFile\Version
     */
    public function import($pointer, $filename = false, $fr = false)
    {
        if ($filename == false) {
            // determine filename from $pointer
            $filename = basename($pointer);
        }

        $fh = Loader::helper('validation/file');
        $fi = Loader::helper('file');
        $cf = Core::make('helper/concrete/file');
        $sanitizedFilename = $fi->sanitize($filename);

        // test if file is valid, else return FileImporter::E_FILE_INVALID
        if (!$fh->file($pointer)) {
            return self::E_FILE_INVALID;
        }

        if (!$fh->extension($filename)) {
            return self::E_FILE_INVALID_EXTENSION;
        }

        if ($fr instanceof FileEntity) {
            $fsl = $fr->getFileStorageLocationObject();
        } else {
            $fsl = StorageLocation::getDefault();
        }
        if (!($fsl instanceof \Concrete\Core\Entity\File\StorageLocation\StorageLocation)) {
            return self::E_FILE_INVALID_STORAGE_LOCATION;
        }

        // store the file in the file storage location.
        $filesystem = $fsl->getFileSystemObject();
        $prefix = $this->generatePrefix();

        try {
            $src = fopen($pointer, 'rb');
            $filesystem->writeStream($cf->prefix($prefix, $sanitizedFilename), $src, array(
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => Core::make('helper/mime')->mimeFromExtension($fi->getExtension($sanitizedFilename)),
            ));
        } catch (\Exception $e) {
            return self::E_FILE_UNABLE_TO_STORE;
        }

        if (!($fr instanceof FileEntity)) {
            // we have to create a new file object for this file version
            $fv = File::add($sanitizedFilename, $prefix, array('fvTitle' => $filename), $fsl, $fr);

            foreach ($this->importProcessors as $processor) {
                if ($processor->shouldProcess($fv)) {
                    $processor->process($fv);
                }
            }

            $fv->refreshAttributes($this->rescanThumbnailsOnImport);
        } else {
            // We get a new version to modify
            $fv = $fr->getVersionToModify(true);
            $fv->updateFile($sanitizedFilename, $prefix);
            $fv->refreshAttributes($this->rescanThumbnailsOnImport);
        }

        return $fv;
    }

    /**
     * Imports a file in the default file storage location's incoming directory.
     *
     * @param string $filename
     * @param File|FileFolder|bool $fr
     *
     * @return number Error Code | \Concrete\Core\Entity\File\Version
     */
    public function importIncomingFile($filename, $fr = false)
    {
        $fh = Loader::helper('validation/file');
        $fi = Loader::helper('file');
        $cf = Core::make('helper/concrete/file');
        $sanitizedFilename = $fi->sanitize($filename);

        $default = StorageLocation::getDefault();
        $storage = $default->getFileSystemObject();

        if (!$storage->has(REL_DIR_FILES_INCOMING . '/' . $filename)) {
            return self::E_FILE_INVALID;
        }

        if (!$fh->extension($filename)) {
            return self::E_FILE_INVALID_EXTENSION;
        }

        // first we import the file into the storage location that is the same.
        $prefix = $this->generatePrefix();
        try {
            $copied = $storage->copy(REL_DIR_FILES_INCOMING . '/' . $filename, $cf->prefix($prefix, $sanitizedFilename));
        } catch (\Exception $e) {
            $copied = false;
        }
        if (!$copied) {
            $storage->writeStream(
                $cf->prefix($prefix, $sanitizedFilename),
                $storage->readStream(REL_DIR_FILES_INCOMING . '/' . $filename)
            );
        }

        if (!($fr instanceof FileEntity)) {
            // we have to create a new file object for this file version
            $fv = File::add($sanitizedFilename, $prefix, array('fvTitle' => $filename), $default, $fr);
            $fv->refreshAttributes($this->rescanThumbnailsOnImport);

            foreach ($this->importProcessors as $processor) {
                if ($processor->shouldProcess($fv)) {
                    $processor->process($fv);
                }
            }
        } else {
            // We get a new version to modify
            $fv = $fr->getVersionToModify(true);
            $fv->updateFile($sanitizedFilename, $prefix);
            $fv->refreshAttributes($this->rescanThumbnailsOnImport);
        }

        return $fv;
    }

    public function setRescanThumbnailsOnImport($refresh)
    {
        $this->rescanThumbnailsOnImport = $refresh;
    }
}
