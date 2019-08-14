<?php
namespace Concrete\Core\Support\Facade;

class Application extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'app';
    }

    /**
     * @since 5.7.4
     */
    public static function getApplicationRelativePath()
    {
        $cms = static::getFacadeApplication();

        return $cms['app_relative_path'];
    }

    /**
     * @since 5.7.4
     */
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
