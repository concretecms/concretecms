<?php
namespace Concrete\Core\Asset;

use HtmlObject\Element;
use Concrete\Core\Html\Object\HeadLink;
use Config;

class CssAsset extends Asset {

	protected $assetSupportsMinification = true;
	protected $assetSupportsCombination = true;

	public function getAssetDefaultPosition() {
		return Asset::ASSET_POSITION_HEADER;
	}

	public function getAssetType() {return 'css';}

	protected static function getRelativeOutputDirectory() {
		return REL_DIR_FILES_CACHE . '/' . DIRNAME_CSS;
	}

	protected static function getOutputDirectory() {
		if (!file_exists(Config::get('concrete.cache.directory') . '/' . DIRNAME_CSS)) {
			$proceed = @mkdir(Config::get('concrete.cache.directory') . '/' . DIRNAME_CSS);
		} else {
			$proceed = true;
		}
		if ($proceed) {
			return Config::get('concrete.cache.directory') . '/' . DIRNAME_CSS;
		} else {
			return false;
		}
	}

    static function changePaths( $content, $current_path, $target_path )
    {
        $current_path = rtrim( $current_path, "/" );
        $target_path = rtrim( $target_path, "/" );
        $current_path_slugs = explode( "/", $current_path );
        $target_path_slugs = explode( "/", $target_path );
        $smallest_count = min( count( $current_path_slugs ), count( $target_path_slugs ) );
        for( $i = 0; $i < $smallest_count && $current_path_slugs[$i] === $target_path_slugs[$i]; $i++ );
        $change_prefix = @implode( "/", @array_merge( @array_fill( 0, count( $target_path_slugs ) - $i, ".." ), @array_slice( $current_path_slugs, $i ) ) );
        if( strlen( $change_prefix ) > 0 ) $change_prefix .= "/";

        $content = preg_replace_callback(
            '/
            @import\\s+
            (?:url\\(\\s*)?     # maybe url(
            [\'"]?              # maybe quote
            (.*?)               # 1 = URI
            [\'"]?              # maybe end quote
            (?:\\s*\\))?        # maybe )
            ([a-zA-Z,\\s]*)?    # 2 = media list
            ;                   # end token
            /x',
            function( $m ) use ( $change_prefix ) {
                $url = $change_prefix.$m[1];
                $url = str_replace('/./', '/', $url);
                do {
                    $url = preg_replace('@/(?!\\.\\.?)[^/]+/\\.\\.@', '/', $url, 1, $changed);
                } while( $changed );
                return "@import url('$url'){$m[2]};";
            },
            $content
        );
        $content = preg_replace_callback(
            '/url\\(\\s*([^\\)\\s]+)\\s*\\)/',
            function( $m ) use ( $change_prefix ) {
                // $m[1] is either quoted or not
                $quote = ($m[1][0] === "'" || $m[1][0] === '"')
                    ? $m[1][0]
                    : '';
                $url = ($quote === '')
                    ? $m[1]
                    : substr($m[1], 1, strlen($m[1]) - 2);

                if( '/' !== $url[0] && strpos( $url, '//') === FALSE ) {
                    $url = $change_prefix.$url;
                    $url = str_replace('/./', '/', $url);
                    do {
                        $url = preg_replace('@/(?!\\.\\.?)[^/]+/\\.\\.@', '/', $url, 1, $changed);
                    } while( $changed );
                }
                return "url({$quote}{$url}{$quote})";
            },
            $content
        );
        return $content;
    }

    protected static function process($assets, $processFunction) {
		if ($directory = self::getOutputDirectory()) {

			$filename = '';
			for ($i = 0; $i < count($assets); $i++) {
				$asset = $assets[$i];
				$filename .= $asset->getAssetURL();
                $sourceFiles[] = $asset->getAssetURL();
			}
			$filename = sha1($filename);
			$cacheFile = $directory . '/' . $filename . '.css';
			if (!file_exists($cacheFile)) {
				$css = '';
				foreach($assets as $asset) {
                    if ($asset->getAssetPath()) {
                        $css .= file_get_contents($asset->getAssetPath()) . "\n\n";
                    }
					$css = $processFunction($css, $asset->getAssetURLPath(), self::getRelativeOutputDirectory());
				}
				@file_put_contents($cacheFile, $css);
			}

			$asset = new CSSAsset();
			$asset->setAssetURL(self::getRelativeOutputDirectory() . '/' . $filename . '.css');
			$asset->setAssetPath($directory . '/' . $filename . '.css');
            $asset->setCombinedAssetSourceFiles($sourceFiles);
			return array($asset);
		}
		return $assets;
    }

	public function combine($assets) {
		return self::process($assets, function($css, $assetPath, $targetPath) {
			return CSSAsset::changePaths($css, $assetPath, $targetPath);
		});
	}

	public function minify($assets) {
		return self::process($assets, function($css, $assetPath, $targetPath) {
			return \CssMin::minify(CSSAsset::changePaths($css, $assetPath, $targetPath));
		});
	}

	public function __toString() {
        $e = new HeadLink($this->getAssetURL(), 'stylesheet', 'text/css', 'all');
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
