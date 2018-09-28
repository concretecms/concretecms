<?php

namespace Concrete\Core\File\Type\Inspector;

use Concrete\Core\Entity\File\Version;
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
            $this->updateSize($fv, $image);
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
}
