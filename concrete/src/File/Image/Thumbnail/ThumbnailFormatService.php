<?php

namespace Concrete\Core\File\Image\Thumbnail;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Service\File as FileService;

class ThumbnailFormatService
{
    /**
     * @deprecated Use \Concrete\Core\File\Image\BitmapFormat::FORMAT_PNG
     */
    const FORMAT_PNG = BitmapFormat::FORMAT_PNG;

    /**
     * @deprecated Use \Concrete\Core\File\Image\BitmapFormat::FORMAT_PNG
     */
    const FORMAT_JPEG = BitmapFormat::FORMAT_JPEG;

    /**
     * Thumbnail format: automatic.
     *
     * @var string
     */
    const FORMAT_AUTO = 'auto';

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var BitmapFormat
     */
    protected $bitmapFormat;

    /**
     * @param Repository $config
     * @param FileService $fileService
     * @param BitmapFormat $bitmapFormat
     */
    public function __construct(Repository $config, FileService $fileService, BitmapFormat $bitmapFormat)
    {
        $this->config = $config;
        $this->fileService = $fileService;
        $this->bitmapFormat = $bitmapFormat;
    }

    /**
     * Get the format to be used for a specific file (using the configured format option).
     *
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|string $file A File of file Version instance, or a file name
     *
     * @return string One of the \Concrete\Core\File\Image\BitmapFormat::FORMAT_... constants
     */
    public function getFormatForFile($file)
    {
        $format = $this->getConfiguredFormat();
        if ($format === static::FORMAT_AUTO) {
            $format = $this->getAutomaticFormatForFile($file);
        }

        return $format;
    }

    /**
     * Get the format to be used for a specific file (calculating if from the file extension).
     *
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|string $file A File of file Version instance, or a file name
     *
     * @return string One of the \Concrete\Core\File\Image\BitmapFormat::FORMAT_... constants
     */
    public function getAutomaticFormatForFile($file)
    {
        if ($file instanceof File) {
            $file = $file->getApprovedVersion();
        }
        if ($file instanceof FileVersion) {
            $file = $file->getFileName();
        }
        $file = (string) $file;
        $extension = $this->fileService->getExtension($file);

        return $this->getAutomaticFormatForFileExtension($extension);
    }

    /**
     * Get the format to be used for a specific file extension (using the configured format option).
     *
     * @param string $extension
     *
     * @return string One of the \Concrete\Core\File\Image\BitmapFormat::FORMAT_... constants
     */
    public function getFormatForFileExtension($extension)
    {
        $format = $this->getConfiguredFormat();
        if ($format === static::FORMAT_AUTO) {
            $format = $this->getAutomaticFormatForFileExtension((string) $extension);
        }

        return $format;
    }

    /**
     * Get the format to be used for a specific file extension (calculating if from the file extension).
     *
     * @param string $extension the file extension (with or without a leading dot)
     *
     * @return string One of the \Concrete\Core\File\Image\BitmapFormat::FORMAT_... constants
     */
    public function getAutomaticFormatForFileExtension($extension)
    {
        return preg_match('/^\.?p?jpe?g$/i', $extension) ? BitmapFormat::FORMAT_JPEG : BitmapFormat::FORMAT_PNG;
    }

    /**
     * Get the configured format.
     *
     * @return string One of the \Concrete\Core\File\Image\BitmapFormat::FORMAT_... constants, or ThumbnailFormatService::FORMAT_AUTO
     */
    protected function getConfiguredFormat()
    {
        $format = $this->config->get('concrete.misc.default_thumbnail_format');
        if ($format === static::FORMAT_AUTO || $this->bitmapFormat->isFormatValid($format)) {
            $result = $format;
        } elseif ($format === 'jpg') { // legacy value
            $result = BitmapFormat::FORMAT_JPEG;
        } else {
            $result = static::FORMAT_AUTO;
        }

        return $result;
    }
}
