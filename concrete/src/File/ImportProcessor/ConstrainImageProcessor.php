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
        return $version->getTypeObject()->getGenericType() == Type::T_IMAGE;
    }

    public function process(Version $version)
    {
        $fr = $version->getFileResource();
        $image = \Image::load($fr->read());
        $fr = $version->getFileResource();
        $width = $this->getMaxWidth();
        $height = $this->getMaxHeight();
        $mode = $this->getConstraintMode();
        $thumbnail = $image->thumbnail(new Box($width, $height), $mode);
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
