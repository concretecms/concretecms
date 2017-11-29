<?php

namespace Concrete\Core\File\Image\Thumbnail;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Service\File as FileService;

class ThumbnailFormatService
{
    /**
     * Thumbnail format: PNG.
     *
     * @var string
     */
    const FORMAT_PNG = 'png';

    /**
     * Thumbnail format: JPEG.
     *
     * @var string
     */
    const FORMAT_JPEG = 'jpeg';

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
     * @param Repository $config
     * @param FileService $fileService
     */
    public function __construct(Repository $config, FileService $fileService)
    {
        $this->config = $config;
        $this->fileService = $fileService;
    }

    /**
     * Get the format to be used for a specific file (using the configured format option).
     *
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|string $file A File of file Version instance, or a file name
     *
     * @return string One of the ThumbnailFormatService::FORMAT_... constants (except FORMAT_AUTO)
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
     * @return string One of the ThumbnailFormatService::FORMAT_... constants (except FORMAT_AUTO)
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
     * @return string One of the ThumbnailFormatService::FORMAT_... constants (except FORMAT_AUTO)
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
     * @return string One of the ThumbnailFormatService::FORMAT_... constants (except FORMAT_AUTO)
     */
    public function getAutomaticFormatForFileExtension($extension)
    {
        return preg_match('/^\.?p?jpe?g$/i', $extension) ? static::FORMAT_JPEG : static::FORMAT_PNG;
    }

    /**
     * Get the configured format.
     *
     * @return string One of the FORMAT_... constants
     */
    protected function getConfiguredFormat()
    {
        $format = $this->config->get('concrete.misc.default_thumbnail_format');
        switch ($format) {
            case static::FORMAT_PNG:
            case static::FORMAT_JPEG:
            case static::FORMAT_AUTO:
                return $format;
            case 'jpg':
                return static::FORMAT_JPEG;
            default:
                return static::FORMAT_AUTO;
        }
    }
}
