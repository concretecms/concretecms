<?php
/**
 * Created by PhpStorm.
 * User: Korvin
 * Date: 7/25/15
 * Time: 8:35 AM.
 */
namespace Concrete\Core\Asset;

interface AssetInterface
{
    public function getAssetDefaultPosition();

    public function getAssetType();

    public function getOutputAssetType();

    /**
     * @param Asset[] $assets
     *
     * @return Asset[]
     *
     * @abstract
     */
    public static function process($assets);

    /**
     * @return bool
     */
    public function assetSupportsMinification();

    /**
     * @return bool
     */
    public function assetSupportsCombination();

    /**
     * @param bool $minify
     */
    public function setAssetSupportsMinification($minify);

    /**
     * @param bool $combine
     */
    public function setAssetSupportsCombination($combine);

    /**
     * @return string
     */
    public function getAssetURL();

    /**
     * @return string
     */
    public function getAssetHashKey();

    /**
     * @return string
     */
    public function getAssetPath();

    /**
     * @return string
     */
    public function getAssetHandle();

    /**
     * @return string
     */
    public function getAssetFilename();

    /**
     * @param string $version
     */
    public function setAssetVersion($version);

    /**
     * @param array $paths
     */
    public function setCombinedAssetSourceFiles($paths);

    /**
     * @return string
     */
    public function getAssetVersion();

    /**
     * @param string $position
     */
    public function setAssetPosition($position);

    /**
     * @param \Package $pkg
     */
    public function setPackageObject($pkg);

    /**
     * @param string $url
     */
    public function setAssetURL($url);

    /**
     * @param string $path
     */
    public function setAssetPath($path);

    /**
     * @return string
     */
    public function getAssetURLPath();

    /**
     * @return bool
     */
    public function isAssetLocal();

    /**
     * @param bool $isLocal
     */
    public function setAssetIsLocal($isLocal);

    /**
     * @return string
     */
    public function getAssetPosition();

    /**
     * @param string $path
     */
    public function mapAssetLocation($path);

    /**
     * @return string|null
     */
    public function getAssetContents();

    public function register($filename, $args, $pkg = false);

    public function __toString();
}
