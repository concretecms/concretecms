<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_CSSAsset extends Asset {
	
	protected $assetSupportsMinification = true;

	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_HEADER;
	}

	public function getAssetType() {return 'css';}

	public function minify($assets) {
		if (!file_exists(DIR_FILES_CACHE . '/' . DIRNAME_CSS)) {
			$proceed = @mkdir(DIR_FILES_CACHE . '/' . DIRNAME_CSS);
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
			$cacheFile = DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $filename . '.css';
			if (!file_exists($cacheFile)) {
				Loader::library('3rdparty/cssmin');
				$css = '';
				foreach($assets as $asset) {
					$css .= file_get_contents($asset->getAssetPath()) . "\n\n";
				}
				$css = CssMin::minify($css);
				@file_put_contents($cacheFile, $css);
			}
		
			$asset = new CSSAsset();
			$asset->setAssetURL(REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $filename . '.css');
			$asset->setAssetPath(DIR_FILES_CACHE . '/' . DIRNAME_CSS . '/' . $filename . '.css');
			return array($asset);
		}
		
		return $assets;
	}

	public function __toString() {
		return '<link rel="stylesheet" type="text/css" href="' . $this->getAssetURL() . '" />';
	}

}