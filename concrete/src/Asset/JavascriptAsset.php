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
    public static function getRelativeOutputDirectory()
    {
        return REL_DIR_FILES_CACHE.'/'.DIRNAME_JAVASCRIPT;
    }

    /**
     * @return bool|string
     */
    protected static function getOutputDirectory()
    {
        if (!file_exists(Config::get('concrete.cache.directory').'/'.DIRNAME_JAVASCRIPT)) {
            $proceed = @mkdir(Config::get('concrete.cache.directory').'/'.DIRNAME_JAVASCRIPT);
        } else {
            $proceed = true;
        }
        if ($proceed) {
            return Config::get('concrete.cache.directory').'/'.DIRNAME_JAVASCRIPT;
        } else {
            return false;
        }
    }

    /**
     * @param Asset[] $assets
     *
     * @return Asset[]
     */
    public static function process($assets)
    {
        if ($directory = self::getOutputDirectory()) {
            $relativeDirectory = self::getRelativeOutputDirectory();
            $filename = '';
            $sourceFiles = array();
            for ($i = 0; $i < count($assets); ++$i) {
                $asset = $assets[$i];
                $filename .= $asset->getAssetHashKey();
                $sourceFiles[] = $asset->getAssetURL();
            }
            $filename = sha1($filename);
            $cacheFile = $directory.'/'.$filename.'.js';
            if (!file_exists($cacheFile)) {
                $js = '';
                foreach ($assets as $asset) {
                    $contents = $asset->getAssetContents();
                    if (isset($contents)) {
                        if ($asset->assetSupportsMinification()) {
                            $contents = \JShrink\Minifier::minify($contents);
                        }
                        $js .= $contents.";\n\n";
                    }
                }
                @file_put_contents($cacheFile, $js);
            }

            $asset = new self();
            $asset->setAssetURL($relativeDirectory.'/'.$filename.'.js');
            $asset->setAssetPath($directory.'/'.$filename.'.js');
            $asset->setCombinedAssetSourceFiles($sourceFiles);

            return array($asset);
        }

        return $assets;
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
            foreach ($this->combinedAssetSourceFiles as $file) {
                $source .= $file.' ';
            }
            $source = trim($source);
            $e->setAttribute('data-source', $source);
        }

        return (string) $e;
    }
}
