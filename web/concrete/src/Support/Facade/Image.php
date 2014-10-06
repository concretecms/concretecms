<?php
namespace Concrete\Core\Support\Facade;

class Image extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'image/gd';
    }

}
