<?php
namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\BitmapFormat;

class SetJPEGQualityProcessor implements ProcessorInterface
{
    protected $quality;

    public function __construct($quality)
    {
        $this->quality = $quality;
    }

    /**
     * @return mixed
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param mixed $quality
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
    }

    public function shouldProcess(Version $version)
    {
        return $version->getTypeObject()->getName() == 'JPEG';
    }

    public function process(Version $version)
    {
        $image = $version->getImagineImage();
        $version->updateContents($image->get(BitmapFormat::FORMAT_JPEG, array('jpeg_quality' => $this->getQuality())));
    }
}
