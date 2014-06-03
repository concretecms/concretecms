<?
namespace Concrete\Core\Legacy;

use Image;
use \Imagine\Image\Box;
use \Imagine\Image\Point\Center;

class ImageHelper
{

    /**
     * Deprecated. Use the Image facade instead.
     * @deprecated
     */
    public function create($originalPath, $newPath, $width, $height, $fit = false)
    {

        if ($fit) {
            $box = new Box($width, $height);
            $center = new Center($box);
            return Image::open($originalPath)
                ->crop($center, $box)->save($newPath);
        } else {
            return Image::open($originalPath)
                ->thumbnail(new Box($width, $height))->save($newPath);
        }


    }

}
