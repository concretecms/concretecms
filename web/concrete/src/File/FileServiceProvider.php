<?php
namespace Concrete\Core\File;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = array(
            'helper/file' => '\Concrete\Core\File\Service\File',
            'helper/concrete/file' => '\Concrete\Core\File\Service\Application',
            'helper/image' => '\Concrete\Core\File\Image\BasicThumbnailer',
            'helper/mime' => '\Concrete\Core\File\Service\Mime',
            'helper/zip' => '\Concrete\Core\File\Service\Zip',
        );

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }

        $this->app->bind('image/imagick', '\Imagine\Imagick\Imagine');
        $this->app->bind('image/gd', '\Imagine\Gd\Imagine');
        $this->app->bind('image/thumbnailer', '\Concrete\Core\File\Image\BasicThumbnailer');
    }
}
