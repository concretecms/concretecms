<?
/**
 * @deprecated
 */
class Loader {

	public static function helper($helper, $pkgHandle = false) {
		return helper($helper, $pkgHandle);
	}

	/** 
	 * @access private
	 */
	public function packageElement($file, $pkgHandle, $args = null) {
		self::element($file, $args, $pkgHandle);
	}

	public function element($_file, $args = null, $_pkgHandle= null) {
		return Concrete\Core\View\View::element($_file, $args, $_pkgHandle);
	}



}