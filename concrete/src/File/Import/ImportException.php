<?php

namespace Concrete\Core\File\Import;

use Concrete\Core\Error\UserMessageException;

class ImportException extends UserMessageException
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
     * concrete5 internal error: missing root folder object.
     *
     * @var int
     */
    const E_FILE_MISSING_ROOT_FOLDER = 21;

    /**
     * Create a new instance given the error code.
     *
     * @param string $code
     * @param \Exception|\Throwable|null $innerException
     *
     * @return static
     */
    public static function fromErrorCode($code, $innerException = null)
    {
        return new static(static::describeErrorCode($code), $code, $innerException);
    }

    /**
     * Returns a text string explaining the error that was passed.
     *
     * @param int $code
     *
     * @return string
     */
    public static function describeErrorCode($code)
    {
        switch ($code) {
            case static::E_PHP_NO_FILE:
            case static::E_FILE_INVALID:
                return t('Invalid file.');
            case static::E_FILE_INVALID_EXTENSION:
                return t('Invalid file extension.');
            case static::E_PHP_FILE_PARTIAL_UPLOAD:
                return t('The file was only partially uploaded.');
            case static::E_FILE_INVALID_STORAGE_LOCATION:
                return t('No default file storage location could be found to store this file.');
            case static::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE:
                return t('Uploaded file is too large. The current value of post_max_filesize is %s', ini_get('post_max_size'));
            case static::E_PHP_FILE_EXCEEDS_HTML_MAX_FILE_SIZE:
            case static::E_PHP_FILE_EXCEEDS_UPLOAD_MAX_FILESIZE:
                return t('Uploaded file is too large. The current value of upload_max_filesize is %s', ini_get('upload_max_filesize'));
            case static::E_FILE_UNABLE_TO_STORE:
                return t('Unable to copy file to the storage location. Please check the settings for the storage location.');
            case static::E_FILE_UNABLE_TO_STORE_PREFIX_PROVIDED:
                return t('Unable to copy file to storage location. This file already exists in your site, or there is insufficient disk space for this operation.');
            case static::E_PHP_NO_TMP_DIR:
                return t('Missing a temporary folder.');
            case static::E_PHP_CANT_WRITE:
                return t('Failed to write file to disk.');
            case static::E_PHP_CANT_WRITE:
                return t('A PHP extension stopped the file upload.');
            case static::E_FILE_MISSING_ROOT_FOLDER:
                return t('The root folder does not exist.');
            case static::E_PHP_FILE_ERROR_DEFAULT:
            default:
                return t("An unknown error occurred while uploading the file. Please check that file uploads are enabled, and that your file does not exceed the size of the post_max_size or upload_max_filesize variables.\n\nFile Uploads: %s\nMax Upload File Size: %s\nPost Max Size: %s", ini_get('file_uploads'), ini_get('upload_max_filesize'), ini_get('post_max_size'));
        }
    }
}
