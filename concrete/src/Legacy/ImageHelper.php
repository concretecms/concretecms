<?php
namespace Concrete\Core\Legacy;

use Concrete\Core\File\Image\BasicThumbnailer;

/**
 * This is deprecated. It will be removed. Use app('image/thumbnailer') to grab the current
 * class instead.
 *
 * @deprecated
 */
final class ImageHelper extends BasicThumbnailer
{
}
