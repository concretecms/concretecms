<?php
namespace Concrete\Core\File\Image\Thumbnail;

use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Imagine\Image\ImagineInterface;

/**
 * Interface ThumbnailerInterface
 * An interface for classes tasked with creating thumbnails. This interace requires imagine.
 */
interface ThumbnailerInterface
{
    /**
     * Get the storage location to use.
     *
     * @return StorageLocationInterface
     */
    public function getStorageLocation();

    /**
     * Set the storage location to use. Note that the $savePath is going to be relative to this location.
     *
     * @param StorageLocationInterface $storageLocation
     *
     * @return self
     */
    public function setStorageLocation(StorageLocationInterface $storageLocation);

    /**
     * Overrides the default JPEG compression level per instance of the image helper.
     * This allows for a single-use for a particularly low or high compression value.
     *
     * @param int $level the level of compression (in the range 0...100)
     *
     * @return static
     */
    public function setJpegCompression($level);

    /**
     * Get the currently set JPEG compression level.
     *
     * @return int
     */
    public function getJpegCompression();

    /**
     * Overrides the default PNG compression level per instance of the image helper.
     * This allows for a single-use for a particularly low or high compression value.
     *
     * @param int $level the level of compression (in the range 0...9)
     *
     * @return static
     */
    public function setPngCompression($level);

    /**
     * Get the currently set PNG compression level.
     *
     * @return int
     */
    public function getPngCompression();

    /**
     * Set the format of the generated thumbnails.
     *
     * @param string $thumbnailsFormat one of the \Concrete\Core\File\Image\BitmapFormat::FORMAT_ constants, or \Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService::FORMAT_AUTO
     *
     * @return static
     */
    public function setThumbnailsFormat($thumbnailsFormat);

    /**
     * Get the format of the generated thumbnails.
     *
     * @return string one of the \Concrete\Core\File\Image\BitmapFormat::FORMAT_ constants, or \Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService::FORMAT_AUTO
     */
    public function getThumbnailsFormat();

    /**
     * Create a thumbnail given an image (or a path to an image).
     *
     * @param ImagineInterface|string $image the image for which you want the thumbnail (or its path)
     * @param string $savePath The path to save the thumbnail to
     * @param int|null $width The thumbnail width (may be empty if $fit is false and $height is specified)
     * @param int|null $height The thumbnail height (may be empty if $fit is false and $width is specified)
     * @param bool $fit Fit to bounds?
     */
    public function create($image, $savePath, $width, $height, $fit = false);

    /**
     * Returns a path to the specified item, resized and/or cropped to meet max width and height. $obj can e
     * Returns an object with the following properties: src, width, height.
     *
     * @param \Concrete\Core\Entity\File\File|string $obj a string (path) or a file object
     * @param int|null $maxWidth The maximum width of the thumbnail (may be empty if $crop is false and $maxHeight is specified)
     * @param int|null $maxHeight The maximum height of the thumbnail (may be empty if $crop is false and $maxWidth is specified)
     * @param bool $crop Fit to bounds?
     *
     * @return \stdClass Object that has the following properties: src (the public URL to the file), width (null if unable to determine it), height (null if unable to determine it)
     */
    public function getThumbnail($obj, $maxWidth, $maxHeight, $crop = false);
}
