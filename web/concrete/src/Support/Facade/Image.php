<?php
namespace Concrete\Core\Support\Facade;

class Image extends Facade
{

    public static function getFacadeAccessor()
    {

        /*
        if (class_exists('Imagick')) {
            try {
                $imagick = new \Imagick();
                $v = $imagick->getVersion();
                list($version, $year, $month, $day, $q, $website) = sscanf(
                    $v['versionString'],
                    'ImageMagick %s %04d-%02d-%02d %s %s');

                if (version_compare($version, '6.2.9') >= 0) {
                    return 'image/imagick';
                }
            } catch (\Exception $foo) {
            }
        }*/

        return 'image/gd';
    }

}
