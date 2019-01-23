<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Type\Type as FileType;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use InvalidArgumentException;

class ImageSizeConstrain implements PreProcessorInterface
{
    const PREPROCESSOR_PRIORITY = 100;

    /**
     * Maximum image width (if set).
     *
     * @var int|null
     */
    private $maxWidth;

    /**
     * Maximum image height (if set).
     *
     * @var int|null
     */
    private $maxHeight;

    /**
     * Resize mode (one of the ImageInterface::THUMBNAIL_... constants).
     *
     * @var int
     */
    private $constraintMode = ImageInterface::THUMBNAIL_INSET;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ProcessorInterface::readConfiguration()
     */
    public function readConfiguration(Repository $config)
    {
        return $this
            ->setConstraintMode(ImageInterface::THUMBNAIL_INSET)
            ->setMaxWidth($config->get('concrete.file_manager.restrict_max_width'))
            ->setMaxHeight($config->get('concrete.file_manager.restrict_max_height'))
        ;
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
     * Set the maximum image width.
     *
     * @param int|float|null $value
     *
     * @return $this
     */
    public function setMaxWidth($value)
    {
        $value = (int) round((float) $value);
        $this->maxWidth = $value > 0 ? $value : null;

        return $this;
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
     * Set the maximum image height.
     *
     * @param int|float|null $value
     *
     * @return $this
     */
    public function setMaxHeight($value)
    {
        $value = (int) round((float) $value);
        $this->maxHeight = $value > 0 ? $value : null;

        return $this;
    }

    /**
     * Get the resize mode.
     *
     * @return int One of the ImageInterface::THUMBNAIL_... constants
     *
     * @see \Imagine\Image\ImageInterface
     */
    public function getConstraintMode()
    {
        return $this->constraintMode;
    }

    /**
     * Set the resize mode.
     *
     * @param int $value One of the ImageInterface::THUMBNAIL_... constants
     *
     * @return $this
     *
     * @see \Imagine\Image\ImageInterface
     */
    public function setConstraintMode($value)
    {
        $this->constraintMode = (int) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::getPreProcessPriority()
     */
    public function getPreProcessPriority()
    {
        return static::PREPROCESSOR_PRIORITY;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::shouldPreProcess()
     */
    public function shouldPreProcess(ImportingFile $file, ImportOptions $options)
    {
        if ($this->getMaxWidth() === null && $this->getMaxHeight() === null) {
            return false;
        }
        if ($file->getFileType()->getGenericType() != FileType::T_IMAGE) {
            return false;
        }
        if ($file->getFileType()->isSVG()) {
            return false;
        }
        $image = $file->getImage();
        if ($image === null) {
            return false;
        }
        $imageSize = $image->getSize();
        if ($this->getMaxWidth() === null || $imageSize->getWidth() <= $this->getMaxWidth()) {
            if ($this->getMaxHeight() === null || $imageSize->getHeight() <= $this->getMaxHeight()) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::preProcess()
     */
    public function preProcess(ImportingFile $file, ImportOptions $options)
    {
        $image = $file->getImage();
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
                $newBox = new Box($width, $this->maxHeight);
            }
        }
        if ($newBox !== null) {
            $this->resizeInPlace($image, $newBox, $this->getConstraintMode());
            $file->saveImage();
        }
    }

    /**
     * This function is a copy of the core thumbnail() function,
     * modified to allow thumbnailing without making a copy in memory first as it is not always needed depending on the context.
     *
     * @param \Imagine\Image\ImageInterface $image The image to be resized
     * @param \Imagine\Image\Box $size The size of the image
     * @param string $mode
     * @param string $filter
     *
     * @throws \InvalidArgumentException
     */
    public function resizeInPlace(ImageInterface $image, Box $size, $mode = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET && $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
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
            return;
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
    }
}
