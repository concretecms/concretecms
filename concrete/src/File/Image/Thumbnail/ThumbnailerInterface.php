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
     * Set the storage location to use. Note that the $savePath is going to be relative to this location.
     *
     * @param StorageLocationInterface $location
     *
     * @return self
     */
    public function setStorageLocation(StorageLocationInterface $location);

    /**
     * Create a thumbnail given an image (or a path to an image).
     *
     * @param ImagineInterface|string $image
     * @param string $savePath The path to save the thumbnail to
     * @param int $width
     * @param int $height
     * @param bool $fit Fit to bounds
     */
    public function create($image, $savePath, $width, $height, $fit = false);
}
