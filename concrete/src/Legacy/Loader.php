<?php
namespace Concrete\Core\Legacy;

use Concrete\Core\Support\Facade\Facade;
use View;

/**
 * @deprecated
 */
class Loader
{
    /**
     * @return \Concrete\Core\Database\Connection\Connection
     */
    public static function db()
    {
        $app = Facade::getFacadeApplication();

        return $app->make('database')->connection();
    }

    public static function helper($service, $pkgHandle = false)
    {
        $app = Facade::getFacadeApplication();
        if ($pkgHandle !== false) {
            return $app->make('/packages/' . $pkgHandle . '/helper/' . $service);
        } else {
            return $app->make('helper/' . $service);
        }
    }

    public static function packageElement($file, $pkgHandle, $args = null)
    {
        self::element($file, $args, $pkgHandle);
    }

    public static function element($_file, $args = null, $_pkgHandle = null)
    {
        return View::element($_file, $args, $_pkgHandle);
    }

    public static function model($model, $pkgHandle = false)
    {
        return false;
    }

    public static function library($library, $pkgHandle = false)
    {
        return false;
    }

    public static function controller($item)
    {
        if ($item instanceof \Page) {
            return $item->getController();
        }

        $controller = '\\Concrete\\Controller\\' . camelcase($item);
        $app = Facade::getFacadeApplication();

        return $app->build($controller);
    }
}
