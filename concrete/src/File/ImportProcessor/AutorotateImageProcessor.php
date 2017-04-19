<?php
namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\Support\Facade\Image;
use Imagine\Filter\Basic\Autorotate;
use Imagine\Filter\Transformation;
use Imagine\Image\Metadata\ExifMetadataReader;
use Concrete\Core\Support\Facade\Application;

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
    
    public function shouldProcess(Version $version)
    {
        return function_exists('exif_read_data')
                && $version->getTypeObject()->getName() == 'JPEG';
    }

    public function process(Version $version)
    {
        $fr = $version->getFileResource();
        
        $imagine = $this->app->make(Image::getFacadeAccessor());
        $imagine->setMetadataReader(new ExifMetadataReader);
        $image = $imagine->load($fr->read());
        
        $transformation = new Transformation($imagine);
        $transformation->applyFilter($image, new Autorotate());
        $version->updateContents($image->get('jpg'));
    }
}
