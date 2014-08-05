<?php
namespace Concrete\Core\Support\Facade;
class Image extends Facade {

	public static function getFacadeAccessor() {
        return class_exists('Imagick') ? 'image/imagick' : 'image/gd';
    }


}