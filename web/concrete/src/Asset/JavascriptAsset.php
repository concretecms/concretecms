<?php
namespace Concrete\Core\Asset;
use HtmlObject\Element;
use Config;

class JavascriptAsset extends Asset {

	protected $assetSupportsMinification = true;
	protected $assetSupportsCombination = true;

	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_FOOTER;
	}

	public function getRelativeOutputDirectory() {
		return REL_DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT;
	}

	protected static function getOutputDirectory() {
		if (!file_exists(Config::get('concrete.cache.directory') . '/' . DIRNAME_JAVASCRIPT)) {
			$proceed = @mkdir(Config::get('concrete.cache.directory') . '/' . DIRNAME_JAVASCRIPT);
		} else {
			$proceed = true;
		}
		if ($proceed) {
			return Config::get('concrete.cache.directory') . '/' . DIRNAME_JAVASCRIPT;
		} else {
			return false;
		}
	}

    protected static function process($assets, $processFunction) {
		if ($directory = self::getOutputDirectory()) {
			$filename = '';
            $sourceFiles = array();
			for ($i = 0; $i < count($assets); $i++) {
				$asset = $assets[$i];
				$filename .= $asset->getAssetURL();
                $sourceFiles[] = $asset->getAssetURL();
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
            $asset->setCombinedAssetSourceFiles($sourceFiles);
			return array($asset);
		}
		return $assets;
    }

	public function combine($assets) {
		return self::process($assets, function($js, $assetPath, $targetPath) {
			return $js;
		});
	}

	public function minify($assets) {
		return self::process($assets, function($js, $assetPath, $targetPath) {
			return \JShrink\Minifier::minify($js);
		});
	}

	public function getAssetType() {return 'javascript';}

	public function __toString() {
        $e = new Element('script');
        $e->type('text/javascript')->src($this->getAssetURL());
        if (count($this->combinedAssetSourceFiles)) {
            $source = '';
            foreach($this->combinedAssetSourceFiles as $file) {
                $source .= $file . ' ';
            }
            $source = trim($source);
            $e->setAttribute('data-source', $source);
        }
        return (string) $e;
	}

}
