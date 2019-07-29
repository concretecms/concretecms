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

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @param  array   $parameters
     * @return mixed
     */
    public static function make($abstract, array $parameters = [])
    {
        return static::$app->make($abstract, $parameters);
    }
}
