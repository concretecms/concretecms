<?php
namespace Concrete\Core\Support\Facade;

class Image extends Facade
{
    public static function getFacadeAccessor()
    {
        $library = \Config::get('concrete.file_manager.images.manipulation_library');
        switch ($library) {
            case 'gd':
                return 'image/gd';
            case 'imagick':
                return 'image/imagick';
        }
    }
}
