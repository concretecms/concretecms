<?php
namespace Concrete\Core\Legacy;
use Database;
use Core;
use View;

/**
 * @deprecated
 */
class Loader {

    /**
     * @return \Concrete\Core\Database\Connection\Connection
     */
    public static function db() {
		return Database::getActiveConnection();
	}

	public static function helper($service, $pkgHandle = false) {
		if ($pkgHandle !== false) {
			return Core::make('/packages/' . $pkgHandle . '/helper/' . $service);
		} else {
			return Core::make('helper/' . $service);
		}
	}

	public static function packageElement($file, $pkgHandle, $args = null) {
		self::element($file, $args, $pkgHandle);
	}

	public static function element($_file, $args = null, $_pkgHandle= null) {
		return View::element($_file, $args, $_pkgHandle);
	}

	public static function model($model, $pkgHandle = false) {
		return false;
	}

	public static function library($library, $pkgHandle = false) {
		return false;
	}

    public static function controller($item)
    {
        if ($item instanceof \Page) {
            return $item->getController();
        }

        $controller = '\\Concrete\\Controller\\' . camelcase($item);
        return new $controller();
    }

}
