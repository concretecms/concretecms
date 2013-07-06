<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_Asset {
	
	protected $assetVersion = '1.0';
	
	abstract public function getAssetFiles();

	public static function getByHandle($identifier) {

		$class = Object::camelcase($identifier) . 'Asset';
		return new $class();	

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