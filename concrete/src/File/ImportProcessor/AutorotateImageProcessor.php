<?php

namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;
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

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
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
                    $fr = $version->getFileResource();
                    $medatadaReader = new ExifMetadataReader();
                    $metadata = $medatadaReader->readStream($fr);
                    switch (isset($metadata['ifd0.Orientation']) ? $metadata['ifd0.Orientation'] : null) {
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
        $fr = $version->getFileResource();

        $imagine = $this->app->make(ImagineInterface::class);
        $imagine->setMetadataReader(new ExifMetadataReader());
        $image = $imagine->load($fr->read());

        $transformation = new Transformation($imagine);
        $transformation->applyFilter($image, new Autorotate());
        $version->updateContents($image->get('jpg'));
    }
}
