<?php

declare(strict_types = 1);

namespace Concrete\Core\File\Upload;

use Punic\Unit;

defined('C5_EXECUTE') or die('Access Denied.');

class Dropzone extends ClientSideUploader
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Upload\ClientSideUploader::supportClientSizeImageResizing()
     */
    public function supportClientSizeImageResizing(): bool
    {
        return true;
    }

    /**
     * Get the Dropzone localization options.
     *
     * @return array array keys are the Dropzone configuration key, values are the configuration values
     *
     * @see https://github.com/dropzone/dropzone/blob/main/src/options.js
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
     *
     * @see https://docs.dropzone.dev/configuration/basics/configuration-options
     * @see https://github.com/dropzone/dropzone/blob/main/src/options.js
     */
    public function getConfigurationOptions(): array
    {
        $options = [
            'timeout' => $this->getTimeout() * 1000,
            'chunking' => $this->isChunkingEnabled(),
            'parallelUploads' => $this->getParallelUploads(),
        ] + $this->getLocalizationOptions();
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
}
