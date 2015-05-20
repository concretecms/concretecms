<?php

namespace Concrete\Core\File;

use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Flysystem\AdapterInterface;
use Loader;
use File as ConcreteFile;
use Core;

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
            case Importer::E_PHP_NO_FILE:
            case Importer::E_FILE_INVALID:
                $msg = t('Invalid file.');
                break;
            case Importer::E_FILE_INVALID_EXTENSION:
                $msg = t('Invalid file extension.');
                break;
            case Importer::E_PHP_FILE_PARTIAL_UPLOAD:
                $msg = t('The file was only partially uploaded.');
                break;
            case Importer::E_FILE_INVALID_STORAGE_LOCATION:
                $msg = t('No default file storage location could be found to store this file.');
                break;
            case Importer::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE:
                $msg = t('Uploaded file is too large. The current value of post_max_filesize is %s',
                    ini_get('post_max_size'));
                break;
            case Importer::E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE:
            case Importer::E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE:
                $msg = t('Uploaded file is too large. The current value of upload_max_filesize is %s',
                    ini_get('upload_max_filesize'));
                break;
            case Importer::E_FILE_UNABLE_TO_STORE:
                $msg = t('Unable to copy file to storage location "%s". Please check the settings for the storage location.',
                    $defaultStorage);
                break;
            case Importer::E_PHP_FILE_ERROR_DEFAULT:
            default:
                $msg = t("An unknown error occurred while uploading the file. Please check that file uploads are enabled, and that your file does not exceed the size of the post_max_size or upload_max_filesize variables.\n\nFile Uploads: %s\nMax Upload File Size: %s\nPost Max Size: %s",
                    ini_get('file_uploads'), ini_get('upload_max_filesize'), ini_get('post_max_size'));
                break;
        }

        return $msg;
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
     * @param ConcreteFile|bool $fr
     *
     * @return number Error Code | \Concrete\Core\File\Version
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
            return Importer::E_FILE_INVALID;
        }

        if (!$fh->extension($filename)) {
            return Importer::E_FILE_INVALID_EXTENSION;
        }

        if ($fr instanceof File) {
            $fsl = $fr->getFileStorageLocationObject();
        } else {
            $fsl = StorageLocation::getDefault();
        }
        if (!($fsl instanceof StorageLocation)) {
            return Importer::E_FILE_INVALID_STORAGE_LOCATION;
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

        if (!($fr instanceof File)) {
            // we have to create a new file object for this file version
            $fv = ConcreteFile::add($sanitizedFilename, $prefix, array('fvTitle' => $filename), $fsl);
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
     * @param ConcreteFile|bool $fr
     *
     * @return number Error Code | \Concrete\Core\File\Version
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
            return Importer::E_FILE_INVALID;
        }

        if (!$fh->extension($filename)) {
            return Importer::E_FILE_INVALID_EXTENSION;
        }

        // first we import the file into the storage location that is the same.
        $prefix = $this->generatePrefix();
        try {
            $copied = $storage->copy(REL_DIR_FILES_INCOMING . '/' . $filename, $cf->prefix($prefix, $sanitizedFilename));
        } catch (\Exception $e) {
            $copied = false;
        }
        if (!$copied) {
            $storage->write(
                $cf->prefix($prefix, $sanitizedFilename),
                $storage->read(REL_DIR_FILES_INCOMING . '/' . $filename)
            );
        }

        if (!($fr instanceof File)) {
            // we have to create a new file object for this file version
            $fv = ConcreteFile::add($sanitizedFilename, $prefix, array('fvTitle' => $filename), $default);
            $fv->refreshAttributes($this->rescanThumbnailsOnImport);
            $fr = $fv->getFile();
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
