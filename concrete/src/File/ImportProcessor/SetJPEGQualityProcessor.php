<?php

namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\File\Version;
use Imagine\Image\ImageInterface;

class SetJPEGQualityProcessor implements ProcessorInterface
{

    protected $quality;

    function __construct($quality)
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
        $fr = $version->getFileResource();
        $image = \Image::load($fr->read());
        $version->updateContents($image->get('jpg', array('jpeg_quality' => $this->getQuality())));
    }

}