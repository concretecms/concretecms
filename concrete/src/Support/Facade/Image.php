<?php

namespace Concrete\Core\Support\Facade;

use Imagine\Image\ImagineInterface;

class Image extends Facade
{
    public static function getFacadeAccessor()
    {
        return ImagineInterface::class;
    }
}
