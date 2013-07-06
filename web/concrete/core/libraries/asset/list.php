<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_AssetList {
	
	private static $loc = null;
	protected $assets = array();

	public static function getInstance() {
		if (null === self::$loc) {
			self::$loc = new self;
		}
		return self::$loc;
	}

	public function register($identifier) {
		$env = Environment::get();
		$path = $identifier;
		$found = false;
		while (isset($path)) {
			$rec = $env->getRecord(DIRNAME_LIBRARIES . '/' . DIRNAME_LIBRARIES_ASSET . '/' . DIRNAME_LIBRARIES_ASSET_ASSETS . '/' . $path . '.php');
			if ($rec->exists()) {
				$found = true;
				unset($path);
			} else if (strpos($path, '/') > 0) {
				// we didn't find this path so we're going to go up the segment.
				$path = substr($path, 0, strrpos($path, '/'));
				if ($path == '/' || $path == '') {
					unset($path);
				}
			} else {
				unset($path);
			}
		}

		if ($found) {
			include($rec->file);
		}
	}
		

}