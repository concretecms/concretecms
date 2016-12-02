<?php
namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\File\Version;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Support\Facade\Image;
use Imagine\Filter\Basic\Autorotate;
use Imagine\Filter\Transformation;
use Imagine\Image\Metadata\ExifMetadataReader;

class AutorotateImageProcessor implements ProcessorInterface
{
    public function shouldProcess(Version $version)
    {
        return function_exists('exif_read_data')
                && $version->getTypeObject()->getGenericType() == FileType::T_IMAGE;
    }

    public function process(Version $version)
    {
        $fi = \Core::make('helper/file');
        $ext = $fi->getExtension($version->getFileName());

        $fr = $version->getFileResource();
        $imagine = Image::getFacadeRoot()->setMetadataReader(new ExifMetadataReader());
        $image = $imagine->load($fr->read());
        $transformation = new Transformation($imagine);
        $transformation->applyFilter($image, new Autorotate());

        $version->updateContents($image->get($ext));
    }
}
