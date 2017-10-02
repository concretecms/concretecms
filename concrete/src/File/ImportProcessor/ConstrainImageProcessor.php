<?php
namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\File\Type\Type;
use Concrete\Core\Entity\File\Version;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ConstrainImageProcessor implements ProcessorInterface
{
    protected $maxWidth;
    protected $maxHeight;
    protected $constraintMode = ImageInterface::THUMBNAIL_INSET;
    protected $resizeInPlace = false;

    public function __construct($maxWidth = null, $maxHeight = null, $constraintMode = null)
    {
        if ($maxWidth) {
            $this->maxWidth = $maxWidth;
        }
        if ($maxHeight) {
            $this->maxHeight = $maxHeight;
        }
        if ($constraintMode && $constraintMode != null) {
            $this->constraintMode = $constraintMode;
        }
    }

    /**
     * @return mixed
     */
    public function getResizeInPlace()
    {
        return $this->resizeInPlace;
    }

    /**
     * @param mixed $ResizeInPlace
     */
    public function setResizeInPlace($resizeInPlace)
    {
        $this->resizeInPlace = $resizeInPlace;
    }

    /**
     * @return mixed
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * @param mixed $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * @return mixed
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * @param mixed $maxHeight
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = $maxHeight;
    }

    /**
     * @return string
     */
    public function getConstraintMode()
    {
        return $this->constraintMode;
    }

    /**
     * @param string $constraintMode
     */
    public function setConstraintMode($constraintMode)
    {
        $this->constraintMode = $constraintMode;
    }

    public function shouldProcess(Version $version)
    {
        $versionTypeObject = $version->getTypeObject();
        if ($versionTypeObject->getGenericType() == Type::T_IMAGE && !$versionTypeObject->isSVG()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return ImageInterface
     */
    public function resizeInPlace(ImageInterface $image, Box $size, $mode = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        // This function is a copy of the core thumbnail() function modified
        // to allow thumbnailing without making a copy in memory first
        // as it is not always needed depending on the context
        
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
        $fr = $version->getFileResource();
        $image = \Image::load($fr->read());
        $fr = $version->getFileResource();
        $width = $this->getMaxWidth();
        $height = $this->getMaxHeight();
        $mode = $this->getConstraintMode();

        // if the image should be processed without making a copy in memory first
        // use $this->replaceInPlace() function
        // Otherwise use normal thumbnailing
        if ($this->getResizeInPlace()) {
            $thumbnail = $this->resizeInPlace($image, new Box($width, $height), $mode);
        } else {
            $thumbnail = $image->thumbnail(new Box($width, $height), $mode);
        }
        $mimetype = $fr->getMimeType();
        $thumbnailOptions = array();
        switch ($mimetype) {
            case 'image/jpeg':
                $thumbnailType = 'jpeg';
                $thumbnailOptions = array('jpeg_quality' => \Config::get('concrete.misc.default_jpeg_image_compression'));
                break;
            case 'image/png':
                $thumbnailType = 'png';
                break;
            case 'image/gif':
                $thumbnailType = 'gif';
                break;
            case 'image/xbm':
                $thumbnailType = 'xbm';
                break;
            case 'image/vnd.wap.wbmp':
                $thumbnailType = 'wbmp';
                break;
            default:
                $thumbnailType = 'png';
                break;
        }

        $version->updateContents($thumbnail->get($thumbnailType, $thumbnailOptions));
    }
}
