<?php

namespace Concrete\Core\Asset;

use Concrete\Core\Html\Object\HeadLink;
use Config;

class CssAsset extends Asset
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
        return Asset::ASSET_POSITION_HEADER;
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return 'css';
    }

    /**
     * @return string
     */
    protected static function getRelativeOutputDirectory()
    {
        return REL_DIR_FILES_CACHE.'/'.DIRNAME_CSS;
    }

    /**
     * @return bool|string
     */
    protected static function getOutputDirectory()
    {
        if (!file_exists(Config::get('concrete.cache.directory').'/'.DIRNAME_CSS)) {
            $proceed = @mkdir(Config::get('concrete.cache.directory').'/'.DIRNAME_CSS);
        } else {
            $proceed = true;
        }
        if ($proceed) {
            return Config::get('concrete.cache.directory').'/'.DIRNAME_CSS;
        } else {
            return false;
        }
    }

    /**
     * @param string $content
     * @param string $current_path
     * @param string $target_path
     *
     * @return string
     */
    public static function changePaths($content, $current_path, $target_path)
    {
        $current_path = rtrim($current_path, '/');
        $target_path = rtrim($target_path, '/');
        $current_path_slugs = explode('/', $current_path);
        $target_path_slugs = explode('/', $target_path);
        $smallest_count = min(count($current_path_slugs), count($target_path_slugs));
        for ($i = 0; $i < $smallest_count && $current_path_slugs[$i] === $target_path_slugs[$i]; $i++);
        $change_prefix = @implode('/', @array_merge(@array_fill(0, count($target_path_slugs) - $i, '..'), @array_slice($current_path_slugs, $i)));
        if (strlen($change_prefix) > 0) {
            $change_prefix .= '/';
        }

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
            function ($m) use ($change_prefix) {
                if (preg_match('@^https?://@i', $m[1])) {
                    $result = $m[0];
                } else {
                    $url = $change_prefix.$m[1];
                    $url = str_replace('/./', '/', $url);
                    do {
                        $url = preg_replace('@/(?!\\.\\.?)[^/]+/\\.\\.@', '/', $url, 1, $changed);
                    } while ($changed);
                    $result = "@import url('$url'){$m[2]};";
                }

                return $result;
            },
            $content
        );
        $content = preg_replace_callback(
            '/url\\(\\s*([^\\)\\s]+)\\s*\\)/',
            function ($m) use ($change_prefix) {
                // $m[1] is either quoted or not
                $quote = ($m[1][0] === "'" || $m[1][0] === '"')
                    ? $m[1][0]
                    : '';
                $url = ($quote === '')
                    ? $m[1]
                    : substr($m[1], 1, strlen($m[1]) - 2);

                if ('/' !== $url[0] && strpos($url, '//') === false && strpos($url, 'data:') !== 0) {
                    $url = $change_prefix.$url;
                    $url = str_replace('/./', '/', $url);
                    do {
                        $url = preg_replace('@/(?!\\.\\.?)[^/]+/\\.\\.@', '/', $url, 1, $changed);
                    } while ($changed);
                }

                return "url({$quote}{$url}{$quote})";
            },
            $content
        );

        return $content;
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
            for ($i = 0; $i < count($assets); $i++) {
                $asset = $assets[$i];
                $filename .= $asset->getAssetHashKey();
                $sourceFiles[] = $asset->getAssetURL();
            }
            $filename = sha1($filename);
            $cacheFile = $directory.'/'.$filename.'.css';
            if (!file_exists($cacheFile)) {
                $css = '';
                foreach ($assets as $asset) {
                    $contents = $asset->getAssetContents();
                    if (isset($contents)) {
                        $contents = CssAsset::changePaths($contents, $asset->getAssetURLPath(), $relativeDirectory);
                        if ($asset->assetSupportsMinification()) {
                            $contents = \CssMin::minify($contents);
                        }
                        $css .= $contents."\n\n";
                    }
                }
                @file_put_contents($cacheFile, $css);
            }

            $asset = new CssAsset();
            $asset->setAssetURL($relativeDirectory.'/'.$filename.'.css');
            $asset->setAssetPath($directory.'/'.$filename.'.css');
            $asset->setCombinedAssetSourceFiles($sourceFiles);

            return array($asset);
        }

        return $assets;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $e = new HeadLink($this->getAssetURL(), 'stylesheet', 'text/css', 'all');
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
