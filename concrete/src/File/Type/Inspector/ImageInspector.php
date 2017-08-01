<?php
namespace Concrete\Core\File\Type\Inspector;

use Concrete\Core\Entity\File\Version;
use Image;
use FileAttributeKey;
use Core;

class ImageInspector extends Inspector
{
    public function inspect(Version $fv)
    {
        $image = $fv->getImagineImage();
        $data = $image->getSize();

        // sets an attribute - these file attribute keys should be added
        // by the system and "reserved"
        $at1 = FileAttributeKey::getByHandle('width');
        $at2 = FileAttributeKey::getByHandle('height');
        $fv->setAttribute($at1, $data->getWidth());
        $fv->setAttribute($at2, $data->getHeight());

        // Set image aspect ratio if we can.
        if (\Config::get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
            $metadata = $image->metadata();
            if (isset($metadata['ifd0.Orientation'])) {
                \Log::info('EXIF data found: '. $metadata['ifd0.Orientation']);
                switch ($metadata['ifd0.Orientation']) {
                    case 3:
                        $image->rotate(180);
                        $fv->updateContents($image->get($fv->getExtension()));
                        break;
                    case 6:
                        $image->rotate(90);
                        $fv->updateContents($image->get($fv->getExtension()));
                        break;
                    case 8:
                        $image->rotate(-90);
                        $fv->updateContents($image->get($fv->getExtension()));
                        break;
                }
            }
        }
        $fv->releaseImagineImage();
    }
}
