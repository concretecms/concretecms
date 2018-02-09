<?php

namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Type\Type;

class ForceImageFormatProcessor implements ProcessorInterface
{
    const FORMAT_JPEG = 1;

    protected $format;

    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function shouldProcess(Version $version)
    {
        if ($this->getFormat() == self::FORMAT_JPEG) {
            return $version->getTypeObject()->getGenericType() == Type::T_IMAGE
                && $version->getTypeObject()->getName() != 'JPEG';
        }

        return false;
    }

    public function process(Version $version)
    {
        switch ($this->getFormat()) {
            case self::FORMAT_JPEG:
                $format = BitmapFormat::FORMAT_JPEG;
            default:
                $format = BitmapFormat::FORMAT_JPEG;
                break;
        }

        if ($format !== null) {
            $bitmapFormat = \Core::make(BitmapFormat::class);
            $extension = $bitmapFormat->getFormatFileExtension($format);
            $image = $version->getImagineImage();
            $filename = $version->getFileName();
            $service = \Core::make('helper/file');
            $newFilename = $service->replaceExtension($filename, $extension);
            $version->updateContents($image->get($format, $bitmapFormat->getFormatImagineSaveOptions($format)));
            $version->rename($newFilename);
        }
    }
}
