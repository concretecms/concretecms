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
        $cms = static::getFacadeApplication();

        return $cms['app_relative_path'];
    }

    public static function getApplicationURL($asObject = false)
    {
        $cms = static::getFacadeApplication();

        $url = $cms->make('url/canonical');

        if (!$asObject) {
            $url = rtrim((string) $url, '/');
        }

        return $url;
    }
}
