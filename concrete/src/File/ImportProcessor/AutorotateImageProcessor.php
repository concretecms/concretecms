<?php

namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Imagine\Filter\Basic\Autorotate;
use Imagine\Filter\Transformation;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Metadata\ExifMetadataReader;
use Throwable;

class AutorotateImageProcessor implements ProcessorInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Should thumbnails be rescanned when the image is rotated?
     *
     * @var bool
     */
    protected $rescanThumbnails = true;

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
    }

    /**
     * Should thumbnails be rescanned when the image is rotated?
     *
     * @return bool
     */
    public function isRescanThumbnails()
    {
        return $this->rescanThumbnails;
    }
    
    /**
     * Should thumbnails be rescanned when the image is rotated?
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
        if (ExifMetadataReader::isSupported()) {
            if ($version->getTypeObject()->getName() == 'JPEG') {
                try {
                    $metadata = $version->hasImagineImage() ? $version->getImagineImage()->metadata() : null;
                    if ($metadata === null) {
                        $fr = $version->getFileResource();
                        $medatadaReader = new ExifMetadataReader();
                        $metadata = $medatadaReader->readData($fr->read());
                    }
                    switch ($metadata->get('ifd0.Orientation', null)) {
                        case 2: // top-right
                        case 3: // bottom-right
                        case 4: // bottom-left
                        case 5: // left-top
                        case 6: // right-top
                        case 7: // right-bottom
                        case 8: // left-bottom
                            $result = true;
                            break;
                    }
                } catch (Exception $x) {
                } catch (Throwable $x) {
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\ImportProcessor\ProcessorInterface::process()
     */
    public function process(Version $version)
    {
        $image = $version->getImagineImage();
        $transformation = new Transformation($this->app->make(ImagineInterface::class));
        $transformation->applyFilter($image, new Autorotate());
        $format = BitmapFormat::FORMAT_JPEG;
        $saveOptions = $this->app->make(BitmapFormat::class)->getFormatImagineSaveOptions($format);
        $version->updateContents($image->get($format, $saveOptions), $this->rescanThumbnails);
    }
}
