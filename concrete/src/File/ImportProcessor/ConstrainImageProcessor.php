<?php

namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Support\Facade\Application;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use InvalidArgumentException;

class ConstrainImageProcessor implements ProcessorInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Maximum image width (if set).
     *
     * @var int|null
     */
    protected $maxWidth;

    /**
     * Maximum image height (if set).
     *
     * @var int|null
     */
    protected $maxHeight;

    /**
     * Resize mode (one of the ImageInterface::THUMBNAIL_... constants).
     *
     * @var string
     */
    protected $constraintMode = ImageInterface::THUMBNAIL_INSET;

    /**
     * Should thumbnails be rescanned when the image is resized?
     *
     * @var bool
     */
    protected $rescanThumbnails = true;

    /**
     * Initialize the instance.
     *
     * @param int|null $maxWidth the maximum image width (if set)
     * @param int|null $maxHeight the maximum image height (if set)
     * @param string|null $constraintMode The resize mode (one of the ImageInterface::THUMBNAIL_... constants)
     */
    public function __construct($maxWidth = null, $maxHeight = null, $constraintMode = null)
    {
        $this->app = Application::getFacadeApplication();
        $this->setMaxWidth($maxWidth);
        $this->setMaxHeight($maxHeight);
        if ($constraintMode) {
            $this->setConstraintMode($constraintMode);
        }
    }

    /**
     * Get the maximum image width (if set).
     *
     * @return int|null
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * Set the maximum image width (if set).
     *
     * @param int|null $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        $maxWidth = (int) $maxWidth;
        $this->maxWidth = $maxWidth > 0 ? $maxWidth : null;
    }

    /**
     * Get the maximum image height (if set).
     *
     * @return int|null
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * Set the maximum image height (if set).
     *
     * @param int|null $maxHeight
     */
    public function setMaxHeight($maxHeight)
    {
        $maxHeight = (int) $maxHeight;
        $this->maxHeight = $maxHeight > 0 ? $maxHeight : null;
    }

    /**
     * Get the resize mode.
     *
     * @return string One of the ImageInterface::THUMBNAIL_... constants
     */
    public function getConstraintMode()
    {
        return $this->constraintMode;
    }

    /**
     * Set the resize mode.
     *
     * @param string $constraintMode One of the ImageInterface::THUMBNAIL_... constants
     */
    public function setConstraintMode($constraintMode)
    {
        $this->constraintMode = $constraintMode;
    }

    /**
     * Should thumbnails be rescanned when the image is resized?
     *
     * @return bool
     */
    public function isRescanThumbnails()
    {
        return $this->rescanThumbnails;
    }

    /**
     * Should thumbnails be rescanned when the image is resized?
     *
     * @param bool $rescanThumbnails
     *
     * @return $this
     */
    public function setRescanThumbnails($rescanThumbnails)
    {
        $this->rescanThumbnails = (bool) $rescanThumbnails;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\ImportProcessor\ProcessorInterface::shouldProcess()
     */
    public function shouldProcess(Version $version)
    {
        $result = false;
        $versionTypeObject = $version->getTypeObject();
        if ($versionTypeObject->getGenericType() == Type::T_IMAGE && !$versionTypeObject->isSVG()) {
            if ($result === false && $this->maxWidth !== null) {
                $imageWidth = (int) $version->getAttribute('width');
                if ($imageWidth > $this->maxWidth) {
                    $result = true;
                }
            }
            if ($result === false && $this->maxHeight !== null) {
                $imageHeight = (int) $version->getAttribute('height');
                if ($imageHeight > $this->maxHeight) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * This function is a copy of the core thumbnail() function,
     * modified to allow thumbnailing without making a copy in memory first as it is not always needed depending on the context.
     *
     * @param ImageInterface $image The image to be resized
     * @param Box $size The size of the image
     * @param string $mode
     * @param string $filter
     *
     * @throws InvalidArgumentException
     *
     * @return ImageInterface
     */
    public function resizeInPlace(ImageInterface $image, Box $size, $mode = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $imageSize = $image->getSize();
        $ratios = [
            $size->getWidth() / $imageSize->getWidth(),
            $size->getHeight() / $imageSize->getHeight(),
        ];

        $image->strip();
        // if target width is larger than image width
        // AND target height is longer than image height
        if ($size->contains($imageSize)) {
            return $image;
        }

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            if (!$imageSize->contains($size)) {
                $size = new Box(
                    min($imageSize->getWidth(), $size->getWidth()),
                    min($imageSize->getHeight(), $size->getHeight())
                );
            } else {
                $imageSize = $image->getSize()->scale($ratio);
                $image->resize($imageSize, $filter);
            }
            $image->crop(new Point(
                max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
            ), $size);
        } else {
            if (!$imageSize->contains($size)) {
                $imageSize = $imageSize->scale($ratio);
                $image->resize($imageSize, $filter);
            } else {
                $imageSize = $image->getSize()->scale($ratio);
                $image->resize($imageSize, $filter);
            }
        }

        return $image;
    }

    public function process(Version $version)
    {
        $image = $version->getImagineImage();
        $imageSize = $image->getSize();
        $newBox = null;
        if ($this->maxWidth !== null && $this->maxHeight !== null) {
            if ($imageSize->getWidth() > $this->maxWidth || $imageSize->getHeight() > $this->maxHeight) {
                $newBox = new Box($this->maxWidth, $this->maxHeight);
            }
        } elseif ($this->maxWidth !== null) {
            if ($imageSize->getWidth() > $this->maxWidth) {
                $height = $this->maxWidth * $imageSize->getHeight() / $imageSize->getWidth();
                $newBox = new Box($this->maxWidth, $height);
            }
        } elseif ($this->maxHeight !== null) {
            if ($imageSize->getHeight() > $this->maxHeight) {
                $width = $this->maxHeight * $imageSize->getWidth() / $imageSize->getHeight();
            }
        }
        if ($newBox !== null) {
            $fr = $version->getFileResource();
            $mimetype = $fr->getMimeType();
            $thumbnail = $this->resizeInPlace($image, $newBox, $this->getConstraintMode());
            $bitmapFormat = $this->app->make(BitmapFormat::class);
            $thumbnailType = $bitmapFormat->getFormatFromMimeType($mimetype, BitmapFormat::FORMAT_PNG);
            $thumbnailOptions = $bitmapFormat->getFormatImagineSaveOptions($thumbnailType);
            $version->updateContents($thumbnail->get($thumbnailType, $thumbnailOptions), $this->rescanThumbnails);
        }
    }

    /**
     * @return bool
     *
     * @deprecated It's always true
     */
    public function getResizeInPlace()
    {
        return true;
    }

    /**
     * @param bool $resizeInPlace
     *
     * @deprecated It's always true
     */
    public function setResizeInPlace($resizeInPlace)
    {
        $this->resizeInPlace = $resizeInPlace;
    }
}
