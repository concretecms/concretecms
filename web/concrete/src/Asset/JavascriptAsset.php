<?php
namespace Concrete\Core\Asset;

use HtmlObject\Element;
use Config;

class JavascriptAsset extends Asset
{

    /**
     * @var bool
     */
    protected $assetSupportsMinification = true;

    /**
     * @var bool
     */
    protected $assetSupportsCombination = true;

    /**
     * @return string
     */
    public function getAssetDefaultPosition()
    {
        return Asset::ASSET_POSITION_FOOTER;
    }

    /**
     * @return string
     */
    public function getRelativeOutputDirectory()
    {
        return REL_DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT;
    }

    /**
     * @return bool|string
     */
    protected static function getOutputDirectory()
    {
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

    /**
     * @param Asset[] $assets
     * @param $processFunction
     * @return Asset[]
     */
    protected static function process($assets, $processFunction)
    {
        if ($directory = self::getOutputDirectory()) {
            $filename = '';
            $sourceFiles = array();
            for ($i = 0; $i < count($assets); $i++) {
                $asset = $assets[$i];
                $filename .= $asset->getAssetHashKey();
                $sourceFiles[] = $asset->getAssetURL();
            }
            $filename = sha1($filename);
            $cacheFile = $directory . '/' . $filename . '.js';
            if (!file_exists($cacheFile)) {
                $js = '';
                foreach($assets as $asset) {
                    $contents = static::getAssetContents($asset);
                    if (isset($contents)) {
                        $js .= $contents . "\n\n";
                        $js = $processFunction($js, $asset->getAssetURLPath(), self::getRelativeOutputDirectory());
                    }
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

    /**
     * @param Asset[] $assets
     * @return Asset[]
     */
    public static function combine($assets)
    {
        return self::process($assets, function($js, $assetPath, $targetPath) {
            return $js;
        });
    }

    /**
     * @param Asset[] $assets
     * @return Asset[]
     */
    public static function minify($assets)
    {
        return self::process($assets, function($js, $assetPath, $targetPath) {
            return \JShrink\Minifier::minify($js);
        });
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return 'javascript';
    }

    /**
     * @return string
     */
    public function __toString()
    {
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