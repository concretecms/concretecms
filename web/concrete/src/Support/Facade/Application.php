<?php
namespace Concrete\Core\Support\Facade;

class Application extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'app';
    }

    public static function getApplicationRelativePath()
    {
        $cms = static::make('app');
        return $cms['app_relative_path'];
    }

    public static function getApplicationURL($asObject = false)
    {
        $cms = static::make('app');
        $url = (string) $cms['app_url'];
        if ($asObject) {
            $url = \League\Url\Url::createFromUrl($url);
        }
        return $url;
    }

}
