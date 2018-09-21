<?php

namespace Concrete\Core\File;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\ImportProcessor\AutorotateImageProcessor;
use Concrete\Core\File\ImportProcessor\ConstrainImageProcessor;
use Concrete\Core\File\ImportProcessor\ProcessorInterface;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Support\Facade\Application;
use Exception;
use League\Flysystem\AdapterInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Importer
{
    /**
     * Standard PHP error: there is no error, the file uploaded with success.
     *
     * @var int
     */
    const E_PHP_FILE_ERROR_DEFAULT = UPLOAD_ERR_OK;

    /**
     * Standard PHP error: the uploaded file exceeds the upload_max_filesize directive in php.ini.
     *
     * @var int
     */
    const E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE = UPLOAD_ERR_INI_SIZE;

    /**
     * Standard PHP error: the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
     *
     * @var int
     */
    const E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE = UPLOAD_ERR_FORM_SIZE;

    /**
     * Standard PHP error: the uploaded file was only partially uploaded.
     *
     * @var int
     */
    const E_PHP_FILE_PARTIAL_UPLOAD = UPLOAD_ERR_PARTIAL;

    /**
     * Standard PHP error: no file was uploaded.
     *
     * @var int
     */
    const E_PHP_NO_FILE = UPLOAD_ERR_NO_FILE;

    /**
     * Standard PHP error: missing a temporary folder.
     *
     * @var int
     */
    const E_PHP_NO_TMP_DIR = UPLOAD_ERR_NO_TMP_DIR;

    /**
     * Standard PHP error: failed to write file to disk.
     *
     * @var int
     */
    const E_PHP_CANT_WRITE = UPLOAD_ERR_CANT_WRITE;

    /**
     * Standard PHP error: a PHP extension stopped the file upload.
     *
     * @var int
     */
    const E_PHP_EXTENSION = UPLOAD_ERR_EXTENSION;

    /**
     * concrete5 internal error: invalid file extension.
     *
     * @var int
     */
    const E_FILE_INVALID_EXTENSION = 10;

    /**
     * concrete5 internal error: pointer is invalid file, is a directory, etc...
     *
     * @var int
     */
    const E_FILE_INVALID = 11;

    /**
     * concrete5 internal error: unable to copy file to storage location.
     *
     * @var int
     */
    const E_FILE_UNABLE_TO_STORE = 12;

    /**
     * concrete5 internal error: default file storage location not found.
     *
     * @var int
     */
    const E_FILE_INVALID_STORAGE_LOCATION = 13;

    /**
     * concrete5 internal error: unable to copy file to storage location (with provided prefix).
     *
     * @var int
     */
    const E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED = 14;

    /**
     * concrete5 internal error: Uploaded file is too large.
     *
     * @var int
     */
    const E_FILE_EXCEEDS_POST_MAX_FILE_SIZE = 20;

    /**
     * Should thumbnails be scanned when importing an image?
     *
     * @var bool
     */
    protected $rescanThumbnailsOnImport = true;

    /**
     * The list of configured import processors.
     *
     * @var \Concrete\Core\File\ImportProcessor\ProcessorInterface
     */
    protected $importProcessors = [];

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
        $config = $this->app->make('config');
        if ($config->get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
            $processor = new AutorotateImageProcessor();
            $processor->setRescanThumbnails(false);
            $this->addImportProcessor($processor);
        }
        $width = (int) $config->get('concrete.file_manager.restrict_max_width');
        $height = (int) $config->get('concrete.file_manager.restrict_max_height');
        if ($width > 0 || $height > 0) {
            $processor = new ConstrainImageProcessor($width, $height);
            $processor->setRescanThumbnails(false);
            $this->addImportProcessor($processor);
        }
    }

    /**
     * Returns a text string explaining the error that was passed.
     *
     * @param int $code
     *
     * @return string
     */
    public static function getErrorMessage($code)
    {
        $app = Application::getFacadeApplication();
        $defaultStorage = $app->make(StorageLocationFactory::class)->fetchDefault()->getName();
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
            case self::E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED:
                $msg = t('Unable to copy file to storage location "%s". This file already exists in your site, or there is insufficient disk space for this operation.', $defaultStorage);
                break;
            case self::E_PHP_NO_TMP_DIR:
                $msg = t('Missing a temporary folder.');
                break;
            case self::E_PHP_CANT_WRITE:
                $msg = t('Failed to write file to disk.');
                break;
            case self::E_PHP_CANT_WRITE:
                $msg = t('A PHP extension stopped the file upload.');
                break;
            case self::E_PHP_FILE_ERROR_DEFAULT:
            default:
                $msg = t("An unknown error occurred while uploading the file. Please check that file uploads are enabled, and that your file does not exceed the size of the post_max_size or upload_max_filesize variables.\n\nFile Uploads: %s\nMax Upload File Size: %s\nPost Max Size: %s",
                    ini_get('file_uploads'), ini_get('upload_max_filesize'), ini_get('post_max_size'));
                break;
        }

        return $msg;
    }

    /**
     * Add an import processor.
     *
     * @param \Concrete\Core\File\ImportProcessor\ProcessorInterface $processor
     */
    public function addImportProcessor(ProcessorInterface $processor)
    {
        $this->importProcessors[] = $processor;
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
     * Imports a local file into the system.
     *
     * @param string $pointer The path to the file
     * @param string|bool $filename A custom name to give to the file. If not specified, we'll derive it from $pointer.
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Tree\Node\Type\FileFolder|null|false $fr If it's a File entity we assign the newly imported FileVersion object to that File. If it's a FileFolder entiity we'll create a new File in that folder (otherwise the new File will be created in the root folder).
     * @param string|null $prefix the prefix to be used to store the file (if empty we'll generate a new prefix)
     *
     * @return \Concrete\Core\Entity\File\Version|int the imported file version (or an error code in case of problems)
     */
    public function import($pointer, $filename = false, $fr = false, $prefix = null)
    {
        $fh = $this->app->make('helper/validation/file');
        $fi = $this->app->make('helper/file');
        $cf = $this->app->make('helper/concrete/file');

        $filename = (string) $filename;
        if ($filename === '') {
            // determine filename from $pointer
            $filename = basename($pointer);
        }

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
            $fsl = $this->app->make(StorageLocationFactory::class)->fetchDefault();
        }
        if (!($fsl instanceof StorageLocation)) {
            return self::E_FILE_INVALID_STORAGE_LOCATION;
        }

        // store the file in the file storage location.
        $filesystem = $fsl->getFileSystemObject();
        if ($prefix) {
            $prefixIsAutoGenerated = false;
        } else {
            // note, if you pass in a prefix manually, make sure it conforms to standards
            // (e.g. it is 12 digits, numeric only)
            $prefix = $this->generatePrefix();
            $prefixIsAutoGenerated = true;
        }

        $src = @fopen($pointer, 'rb');
        if ($src === false) {
            return self::E_FILE_INVALID;
        }
        try {
            $filesystem->writeStream(
                $cf->prefix($prefix, $sanitizedFilename),
                $src,
                [
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                    'mimetype' => $this->app->make('helper/mime')->mimeFromExtension($fi->getExtension($sanitizedFilename)),
                ]
            );
        } catch (Exception $e) {
            if (!$prefixIsAutoGenerated) {
                return self::E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED;
            } else {
                return self::E_FILE_UNABLE_TO_STORE;
            }
        } finally {
            @fclose($src);
        }

        if (!($fr instanceof FileEntity)) {
            // we have to create a new file object for this file version
            $fv = File::add($sanitizedFilename, $prefix, ['fvTitle' => $filename], $fsl, $fr);
        } else {
            // We get a new version to modify
            $fv = $fr->getVersionToModify(true);
            $fv->updateFile($sanitizedFilename, $prefix);
        }

        $fv->refreshAttributes(false);
        foreach ($this->importProcessors as $processor) {
            if ($processor->shouldProcess($fv)) {
                $processor->process($fv);
            }
        }
        if ($this->rescanThumbnailsOnImport) {
            $fv->rescanThumbnails();
        }
        $fv->releaseImagineImage();

        return $fv;
    }

    /**
     * Import a file in the default file storage location's incoming directory.
     *
     * @param string $filename the name of the file in the incoming directory
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Tree\Node\Type\FileFolder|null|false $fr If it's a File entity we assign the newly imported FileVersion object to that File. If it's a FileFolder entiity we'll create a new File in that folder (otherwise the new File will be created in the root folder).
     *
     * @return \Concrete\Core\Entity\File\Version|int the imported file version (or an error code in case of problems)
     */
    public function importIncomingFile($filename, $fr = false)
    {
        $fh = $this->app->make('helper/validation/file');
        $fi = $this->app->make('helper/file');
        $cf = $this->app->make('helper/concrete/file');

        $sanitizedFilename = $fi->sanitize($filename);

        $default = $this->app->make(StorageLocationFactory::class)->fetchDefault();
        $storage = $default->getFileSystemObject();

        if (!$storage->has(REL_DIR_FILES_INCOMING . '/' . $filename)) {
            return self::E_FILE_INVALID;
        }

        if (!$fh->extension($filename)) {
            return self::E_FILE_INVALID_EXTENSION;
        }

        // first we import the file into the storage location that is the same.
        $prefix = $this->generatePrefix();
        $destinationPath = $cf->prefix($prefix, $sanitizedFilename);
        try {
            $copied = $storage->copy(REL_DIR_FILES_INCOMING . '/' . $filename, $destinationPath);
        } catch (Exception $e) {
            $copied = false;
        }
        if (!$copied) {
            $src = $storage->readStream(REL_DIR_FILES_INCOMING . '/' . $filename);
            if (!$src) {
                return self::E_FILE_INVALID;
            }
            $storage->writeStream($destinationPath, $src);
            @fclose($src);
        }

        if (!($fr instanceof FileEntity)) {
            // we have to create a new file object for this file version
            $fv = File::add($sanitizedFilename, $prefix, ['fvTitle' => $filename], $default, $fr);
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

    /**
     * Import a file received via a POST request to the default file storage location.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile The uploaded file
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Tree\Node\Type\FileFolder|null|false $fr If it's a File entity we assign the newly imported FileVersion object to that File. If it's a FileFolder entiity we'll create a new File in that folder (otherwise the new File will be created in the root folder).
     *
     * @return \Concrete\Core\Entity\File\Version|int the imported file version (or an error code in case of problems)
     *
     * @example
     * <pre><code>
     * $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
     * $request = $app->make(\Concrete\Core\Http\Request::class);
     * $importer = $app->make(\Concrete\Core\File\Importer::class);
     * $fv = $importer->importUploadedFile($request->files->get('field_name'));
     * if (is_int($fv)) {
     *     $errorToShow = $importer->getErrorMessage($fv);
     * }
     * </code></pre>
     */
    public function importUploadedFile(UploadedFile $uploadedFile = null, $fr = false)
    {
        if ($uploadedFile === null) {
            $result = E_PHP_NO_FILE;
        } elseif (!$uploadedFile->isValid()) {
            $result = $uploadedFile->getError();
        } else {
            $result = $this->import($uploadedFile->getPathname(), $uploadedFile->getClientOriginalName(), $fr);
        }

        return $result;
    }

    /**
     * Enable scanning of thumbnails when importing an image?
     *
     * @param bool $refresh
     */
    public function setRescanThumbnailsOnImport($refresh)
    {
        $this->rescanThumbnailsOnImport = $refresh;
    }
}
