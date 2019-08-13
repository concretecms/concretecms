<?php
namespace Concrete\Core\Legacy;

use Concrete\Core\File\Image\BasicThumbnailer;

/**
 * This is deprecated. It will be removed. Use Core::make('image/thumbnailer') to grab the current
 * class instead.
 *
 * @deprecated
 * @since 5.7.0 (but not in 5.7.5)
 */
final class ImageHelper extends BasicThumbnailer
{
}
