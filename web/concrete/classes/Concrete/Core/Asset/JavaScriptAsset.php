<?
namespace Concrete\Core\Asset;
class JavaScriptAsset extends Asset {
	
	protected $assetSupportsMinification = true;
	protected $assetSupportsCombination = true;

	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_FOOTER;
	}

	public function getRelativeOutputDirectory() {
		return REL_DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT;
	}

	protected static function getOutputDirectory() {
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

    protected static function process($assets, $processFunction) {
		if ($directory = self::getOutputDirectory()) {
			$filename = '';
			for ($i = 0; $i < count($assets); $i++) {
				$asset = $assets[$i];
				$filename .= $asset->getAssetURL();
			}
			$filename = sha1($filename);
			$cacheFile = $directory . '/' . $filename . '.js';
			if (!file_exists($cacheFile)) {
				$js = '';
				foreach($assets as $asset) {
					$js .= file_get_contents($asset->getAssetPath()) . "\n\n";
					$js = $processFunction($js, $asset->getAssetURLPath(), self::getRelativeOutputDirectory());
				}
				@file_put_contents($cacheFile, $js);
			}
			
			$asset = new JavascriptAsset();
			$asset->setAssetURL(self::getRelativeOutputDirectory() . '/' . $filename . '.js');
			$asset->setAssetPath($directory . '/' . $filename . '.js');
			return array($asset);
		}
		return $assets;
    }

	public static function combine($assets) {
		return self::process($assets, function($js, $assetPath, $targetPath) {
			return $js;
		});
	}

	public static function minify($assets) {
		Loader::library('3rdparty/jsmin');
		return self::process($assets, function($js, $assetPath, $targetPath) {
			return JSMin::minify($js);
		});
	}

	public function getAssetType() {return 'javascript';}

	public function __toString() {
		return '<script type="text/javascript" src="' . $this->getAssetURL() . '"></script>';
	}

}