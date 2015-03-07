<?php
namespace Concrete\Core\Support\Facade;

use League\Url\Url;

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

    public static function getApplicationURL()
    {
        $cms = static::make('app');
        return Url::createFromUrl((string) $cms['app_url']);
    }

}
