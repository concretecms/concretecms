<?php

namespace Concrete\Core\Asset;

/**
 * Interface that all the assets must implement.
 */
interface AssetInterface
{
    /**
     * Asset position: in the <head> tag.
     *
     * @var string
     */
    const ASSET_POSITION_HEADER = 'H';

    /**
     * Asset position: at the end of the <body> tag.
     *
     * @var string
     */
    const ASSET_POSITION_FOOTER = 'F';

    /**
     * Output asset type: CSS.
     *
     * @var string
     */
    const OUTPUTASSETTYPE_CSS = 'javascript';

    /**
     * Output asset type: JavaScript.
     *
     * @var string
     */
    const OUTPUTASSETTYPE_JAVASCRIPT = 'javascript';

    /**
     * Render the HTML tag that will load this asset.
     *
     * @return string
     */
    public function __toString();

    /**
     * Get the default asset position (\Concrete\Core\Asset\AssetInterface::ASSET_POSITION_HEADER or \Concrete\Core\Asset\AssetInterface::ASSET_POSITION_FOOTER).
     *
     * @return string
     */
    public function getAssetDefaultPosition();

    /**
     * Set the position of this asset (\Concrete\Core\Asset\AssetInterface::ASSET_POSITION_HEADER or \Concrete\Core\Asset\AssetInterface::ASSET_POSITION_FOOTER).
     *
     * @param string $position
     */
    public function setAssetPosition($position);

    /**
     * Get the position of this asset (\Concrete\Core\Asset\AssetInterface::ASSET_POSITION_HEADER or \Concrete\Core\Asset\AssetInterface::ASSET_POSITION_FOOTER).
     *
     * @return string
     */
    public function getAssetPosition();

    /**
     * Get the unique identifier of the asset type.
     *
     * @return string
     */
    public function getAssetType();

    /**
     * Get the handle of this asset (together with getAssetType, identifies this asset).
     *
     * @return string
     */
    public function getAssetHandle();

    /**
     * Get the resulting type of the asset (\Concrete\Core\Asset\AssetInterface::OUTPUTASSETTYPE_CSS, \Concrete\Core\Asset\AssetInterface::OUTPUTASSETTYPE_JAVASCRIPT or other values).
     *
     * @return string
     */
    public function getOutputAssetType();

    /**
     * Is this asset a locally available file (accessible with the getAssetPath method)?
     *
     * @param bool $isLocal
     */
    public function setAssetIsLocal($isLocal);

    /**
     * Is this asset a locally available file (accessible with the getAssetPath method)?
     *
     * @return bool
     */
    public function isAssetLocal();

    /**
     * Does this asset support minification?
     *
     * @param bool $minify
     */
    public function setAssetSupportsMinification($minify);

    /**
     * Does this asset support minification?
     *
     * @return bool
     */
    public function assetSupportsMinification();

    /**
     * Can this asset be combined with other assets?
     *
     * @param bool $combine
     */
    public function setAssetSupportsCombination($combine);

    /**
     * Can this asset be combined with other assets?
     *
     * @return bool
     */
    public function assetSupportsCombination();

    /**
     * Set the URL of this asset.
     *
     * @param string $url
     */
    public function setAssetURL($url);

    /**
     * Get the URL of this asset.
     *
     * @return string
     */
    public function getAssetURL();

    /**
     * Set the path to this asset.
     *
     * @param string $path
     */
    public function setAssetPath($path);

    /**
     * Get the path to this asset.
     *
     * @return string
     */
    public function getAssetPath();

    /**
     * If the asset is local, set its path and URL starting from the relative path. If it's not local, set its URL.
     *
     * @param string $path
     */
    public function mapAssetLocation($path);

    /**
     * Get the path of the parent "folder" that contains this asset.
     *
     * @return string
     */
    public function getAssetURLPath();

    /**
     * Get the name of the file of this asset.
     *
     * @return string
     */
    public function getAssetFilename();

    /**
     * Get a string that unambiguously identifies this asset.
     *
     * @return string
     */
    public function getAssetHashKey();

    /**
     * Set the version of this asset.
     *
     * @param string $version
     */
    public function setAssetVersion($version);

    /**
     * Get the version of this asset.
     *
     * @return string
     */
    public function getAssetVersion();

    /**
     * Set the package that defines this asset.
     *
     * @param \Concrete\Core\Package\Package|\Concrete\Core\Entity\Package|null|false $pkg
     */
    public function setPackageObject($pkg);

    /**
     * Get the contents of the asset (if applicable).
     *
     * @return string|null
     */
    public function getAssetContents();

    /**
     * Set the URL of the source files this asset has been built from (useful to understand the origin of this asset).
     *
     * @param string[] $paths
     */
    public function setCombinedAssetSourceFiles($paths);

    /**
     * Register the asset properties.
     *
     * @param string $filename the location of the asset
     * @param array $args {
     *
     *     @var bool $local is this asset a locally available file (accessible with the getAssetPath method)?
     *     @var bool $minify does this asset support minification?
     *     @var bool $combine can this asset be combined with other assets?
     *     @var string $version the version of this asset
     *     @var string $position the position of this asset (\Concrete\Core\Asset\AssetInterface::ASSET_POSITION_HEADER or \Concrete\Core\Asset\AssetInterface::ASSET_POSITION_FOOTER).
     * }
     *
     * @param \Concrete\Core\Package\Package|\Concrete\Core\Entity\Package|string|null|false $pkg the package that defines this asset (or its handle)
     */
    public function register($filename, $args, $pkg = false);

    /**
     * Asset-type specific post-processing.
     *
     * @param \Concrete\Core\Asset\AssetInterface[] $assets The original assets
     *
     * @return \Concrete\Core\Asset\AssetInterface[] The final assets
     *
     * @example Compress JavaScripts, merge CSS files...
     */
    public static function process($assets);
}
