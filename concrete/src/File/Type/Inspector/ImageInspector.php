<?php

namespace Concrete\Core\File\Type\Inspector;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Imagine\Image\ImageInterface;
use Throwable;

/**
 * An inspector to process image files.
 */
class ImageInspector extends Inspector
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Type\Inspector\Inspector::inspect()
     */
    public function inspect(Version $fv)
    {
        try {
            $image = $fv->getImagineImage() ?: null;
        } catch (Exception $x) {
            $image = null;
        } catch (Throwable $x) {
            $image = null;
        }
        if ($image !== null) {
            $app = Application::getFacadeApplication();
            $config = $app->make('config');
            $this->updateSize($fv, $image);
            if ($config->get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
                $this->fixOrientation($fv, $image);
            }
            $fv->releaseImagineImage();
        }
    }

    /**
     * Update the width and height attributes of the file version starting from the image data.
     *
     * @param Version $fv
     * @param ImageInterface $image
     *
     * @return bool true if success, false otherwise
     */
    private function updateSize(Version $fv, ImageInterface $image)
    {
        $result = false;
        try {
            $size = $image->getSize();
        } catch (Exception $x) {
            $size = null;
        } catch (Throwable $x) {
            $size = null;
        }
        if ($size !== null) {
            $attributeCategory = $fv->getObjectAttributeCategory();
            $atWidth = $attributeCategory->getAttributeKeyByHandle('width');
            if ($atWidth !== null) {
                $fv->setAttribute($atWidth, $size->getWidth());
            }
            $atHeight = $attributeCategory->getAttributeKeyByHandle('height');
            if ($atHeight !== null) {
                $fv->setAttribute($atHeight, $size->getHeight());
            }
            $result = true;
        }

        return $result;
    }

    /**
     * Fix the orientation of the file accordingly to the EXIF metadata.
     *
     * @param Version $fv
     * @param ImageInterface $image
     *
     * @return bool true if changed, false otherwise
     */
    private function fixOrientation(Version $fv, ImageInterface $image)
    {
        $result = false;
        $metadata = $image->metadata();
        if (isset($metadata['ifd0.Orientation'])) {
            switch ($metadata['ifd0.Orientation']) {
                case 3:
                    $image->rotate(180);
                    $fv->updateContents($image->get($fv->getExtension()));
                    $result = true;
                    break;
                case 6:
                    $image->rotate(90);
                    $fv->updateContents($image->get($fv->getExtension()));
                    $result = true;
                    break;
                case 8:
                    $image->rotate(-90);
                    $fv->updateContents($image->get($fv->getExtension()));
                    $result = true;
                    break;
            }
        }

        return $result;
    }
}
