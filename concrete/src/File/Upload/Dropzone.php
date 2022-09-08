<?php

declare(strict_types = 1);

namespace Concrete\Core\File\Upload;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\Utility\Service\Number;
use Punic\Unit;

defined('C5_EXECUTE') or die('Access Denied.');

class Dropzone
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\File\Image\BitmapFormat
     */
    protected $bitmapFormat;

    /**
     * @var \Concrete\Core\Utility\Service\Number
     */
    protected $numberService;

    public function __construct(Repository $config, BitmapFormat $bitmapFormat, Number $numberService)
    {
        $this->config = $config;
        $this->bitmapFormat = $bitmapFormat;
        $this->numberService = $numberService;
    }

    /**
     * Get the Dropzone localization options.
     *
     * @return array array keys are the Dropzone configuration key, values are the configuration values
     */
    public function getLocalizationOptions(): array
    {
        return [
            'dictDefaultMessage' => t('Drop files here or click to upload.'),
            'dictFallbackMessage' => t("Your browser does not support drag'n'drop file uploads."),
            'dictFallbackText' => t('Please use the fallback form below to upload your files like in the olden days.'),
            'dictFileTooBig' => t('File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.'),
            'dictInvalidFileType' => t('You can\'t upload files of this type.'),
            'dictResponseError' => t('Server responded with {{statusCode}} code.'),
            'dictCancelUpload' => t('Cancel upload'),
            'dictUploadCanceled' => t('Upload canceled.'),
            'dictCancelUploadConfirmation' => t('Are you sure you want to cancel this upload?'),
            'dictRemoveFile' => t('Remove file'),
            'dictMaxFilesExceeded' => t('You can not upload any more files.'),
            'dictFileSizeUnits' => [
                'tb' => Unit::getName('digital/terabyte', 'narrow'),
                'gb' => Unit::getName('digital/gigabyte', 'narrow'),
                'mb' => Unit::getName('digital/megabyte', 'narrow'),
                'kb' => Unit::getName('digital/kilobyte', 'narrow'),
                'b' => Unit::getName('digital/byte', 'narrow'),
            ],
        ];
    }

    /**
     * Get the Dropzone configuration options.
     *
     * @return array array keys are the Dropzone configuration key, values are the configuration values
     */
    public function getConfigurationOptions(): array
    {
        $options = [
            'timeout' => $this->getTimeout(),
            'chunking' => (bool) $this->config->get('concrete.upload.chunking.enabled'),
        ] + $this->getLocalizationOptions();
        $parallelUploads = (int) $this->config->get('concrete.upload.parallel');
        if ($parallelUploads > 0) {
            $options['parallelUploads'] = $parallelUploads;
        }
        if ($options['chunking']) {
            // You cannot set both: uploadMultiple and chunking
            $options['uploadMultiple'] = false;
            $options['chunkSize'] = $this->getChunkSize();
        }
        $maxWidth = (int) $this->config->get('concrete.file_manager.restrict_max_width');
        if ($maxWidth > 0) {
            $options['resizeWidth'] = $maxWidth;
        }
        $maxHeight = (int) $this->config->get('concrete.file_manager.restrict_max_height');
        if ($maxHeight > 0) {
            $options['resizeHeight'] = $maxHeight;
        }
        if ($maxWidth > 0 || $maxHeight > 0) {
            $options['resizeQuality'] = $this->bitmapFormat->getDefaultJpegQuality() / 100;
            $options['_dontResizeMimeTypes'] = preg_split('/\s+/', (string) $this->config->get('concrete.file_manager.dont_resize_mimetypes'), -1, PREG_SPLIT_NO_EMPTY);
        }
        return $options;
    }

    private function getTimeout(): int
    {
        $maxExecutionTime = (int) ini_get('max_execution_time');
        $maxInputTime = (int) ini_get('max_input_time');
        $timeout = $maxExecutionTime <= 0 ? 24 * 60 * 60 : $maxExecutionTime;
        if ($maxInputTime === 0) {
            $timeout += 24 * 60 * 60;
        } elseif ($maxInputTime > 0) {
            $timeout += $maxInputTime;
        }
        return $timeout * 1000;
    }

    private function getChunkSize(): int
    {
        $chunkSize = (int) $this->config->get('concrete.upload.chunking.chunkSize');

        return $chunkSize > 0 ? $chunkSize : $this->getDropzoneAutomaticChunkSize();
    }

    private function getDropzoneAutomaticChunkSize(): int
    {
        // Maximum size of an uploaded file, minus a small value (just in case)
        $uploadMaxFilesize = (int) $this->numberService->getBytes(ini_get('upload_max_filesize')) - 100;
        // Max size of post data allowed, minus enough space to consider other posted fields.
        $postMaxSize = (int) $this->numberService->getBytes(ini_get('post_max_size')) - 10000;
        if ($uploadMaxFilesize < 1 && $postMaxSize < 1) {
            return 2000000;
        }
        if ($uploadMaxFilesize < 1) {
            return $postMaxSize;
        }
        if ($postMaxSize < 1) {
            return $uploadMaxFilesize;
        }

        return min($uploadMaxFilesize, $postMaxSize);
    }
}
