<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_JavaScriptAsset extends Asset {
	
	protected $assetSupportsMinification = true;
	protected $assetSupportsCombination = true;

	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_FOOTER;
	}

	protected static function getDirectory() {
		if (!file_exists(DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT)) {
			$proceed = @mkdir(DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT);
		} else {
			$proceed = true;
		}
		if ($proceed) {
			return DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT;
		} else {
			return false;
		}
	}

//				$js = JSMin::minify($js);

	public static function combine($assets) {
		if ($directory = self::getDirectory()) {
			$filename = '';
			for ($i = 0; $i < count($assets); $i++) {
				$asset = $assets[$i];
				$filename .= $asset->getAssetURL();
			}
			$filename = sha1($filename);
			$cacheFile = $directory . '/' . $filename . '.js';
			if (!file_exists($cacheFile)) {
				Loader::library('3rdparty/jsmin');
				$js = '';
				foreach($assets as $asset) {
					$js .= file_get_contents($asset->getAssetPath()) . "\n\n";
				}
				@file_put_contents($cacheFile, $js);
			}
		
			$asset = new JavaScriptAsset();
			$asset->setAssetURL(REL_DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT . '/' . $filename . '.js');
			$asset->setAssetPath($directory . '/' . $filename . '.js');
			return array($asset);
		}

		return $assets;
	}

	public static function minify($assets) {
		if (!file_exists(DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT)) {
			$proceed = @mkdir(DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT);
		} else {
			$proceed = true;
		}
		if ($proceed) {
			$filename = '';
			for ($i = 0; $i < count($assets); $i++) {
				$asset = $assets[$i];
				$filename .= $asset->getAssetURL();
			}
			$filename = sha1($filename);
			$cacheFile = DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT . '/' . $filename . '.js';
			if (!file_exists($cacheFile)) {
				Loader::library('3rdparty/jsmin');
				$js = '';
				foreach($assets as $asset) {
					$js .= file_get_contents($asset->getAssetPath()) . "\n\n";
				}
				$js = JSMin::minify($js);
				@file_put_contents($cacheFile, $js);
			}
		
			$asset = new JavaScriptAsset();
			$asset->setAssetURL(REL_DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT . '/' . $filename . '.js');
			$asset->setAssetPath(DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT . '/' . $filename . '.js');
			return array($asset);
		}

		return $assets;
	}

	public function getAssetType() {return 'javascript';}

	public function __toString() {
		return '<script type="text/javascript" src="' . $this->getAssetURL() . '"></script>';
	}

}