<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_AssetList {
	
	private static $loc = null;
	public $assets = array();
	public $assetGroups = array();

	public function getRegisteredAssets() {
		return $this->assets;
	}

	public static function getInstance() {
		if (null === self::$loc) {
			self::$loc = new self;
		}
		return self::$loc;
	}

	public function register($assetType, $assetHandle, $filename, $weight = false, $position = false, $postprocess = -1) {
		$class = Object::camelcase($assetType) . 'Asset';
		$o = new $class($assetHandle);
		$o->populateAssetURLFromFilename($filename);
		$o->populateAssetPathFromFilename($filename);
		if ($postprocess === true || $postprocess === false) {
			$o->setAssetSupportsPostProcessing($postprocess);
		}
		if ($weight) {
			$o->setAssetWeight($weight);
		}
		if ($position) {
			$o->setAssetPosition($position);
		}
		$this->assets[$assetType][$assetHandle] = $o;
		return $o;
	}

	public function registerGroup($assetGroupHandle, $assetHandles, $customClass = false) {
		if ($customClass) {
			$class = Object::camelcase($assetGroupHandle) . 'AssetGroup';
		} else {
			$class = 'AssetGroup';
		}
		$group = new $class();
		foreach($assetHandles as $assetArray) {
			$group->add($this->getAsset($assetArray[0], $assetArray[1]));
		}
		$this->assetGroups[$assetGroupHandle] = $group;
	}

	public function getAsset($assetType, $assetHandle) {
		return $this->assets[$assetType][$assetHandle];
	}

	public function getAssetGroup($assetGroupHandle) {
		return $this->assetGroups[$assetGroupHandle];
	}

	/*

	public function register($identifier, $pkgHandle = false) {
		$obj = new stdClass;
		$obj->assetHandle = $identifier;
		$obj->pkgHandle = $pkgHandle;
		$this->assets[$identifier] = $obj;
	}


	public function getRegisteredAssetByIdentifier($identifier) {
		$path = $identifier;
		$continue = true;
		while ($continue) {
			if (array_key_exists($identifier, $this->assets)) {
				$continue = false;
			} else {
				// we didn't find this path so we're going to go up the segment.
				$path = substr($path, 0, strrpos($path, '/'));
				if ($path == '/' || $path == '') {
					$continue = false;
				}
			}
		}
		
		return $this->assets[$path];
	}

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
		*/


}