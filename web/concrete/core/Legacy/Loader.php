<?
/**
 * @deprecated
 */
class Loader {

	public static function db() {
		return Database::getActiveConnection();
	}
	
	public static function helper($service, $pkgHandle = false) {
		return Concrete::make('helper/' . $service);
	}

	public static function packageElement($file, $pkgHandle, $args = null) {
		self::element($file, $args, $pkgHandle);
	}

	public static function element($_file, $args = null, $_pkgHandle= null) {
		return View::element($_file, $args, $_pkgHandle);
	}



}