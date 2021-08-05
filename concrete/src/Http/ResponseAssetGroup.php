<?php
namespace Concrete\Core\Http;

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetGroup;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Asset\AssetPointer;
use Monolog\Logger;

class ResponseAssetGroup
{
    /**
     * @var self|null
     */
    protected static $group = null;

    /**
     * @var \Concrete\Core\Asset\AssetPointer[]
     */
    protected $providedAssetGroupUnmatched = array();

    /**
     * @var array
     */
    protected $outputAssets = array();

    /**
     * Get an instance of this singleton.
     *
     * @return self
     */
    public static function get()
    {
        if (null === self::$group) {
            self::$group = new self();
        }

        return self::$group;
    }

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->requiredAssetGroup = new AssetGroup();
        $this->providedAssetGroup = new AssetGroup();
        $this->providedAssetGroupUnmatched = array();
        $this->outputAssets = array();
    }

    /**
     * Add an asset that should be loaded in the header.
     *
     * @param \Concrete\Core\Asset\Asset $item
     */
    public function addHeaderAsset($item)
    {
        $this->addOutputAssetAt($item, Asset::ASSET_POSITION_HEADER);
    }

    /**
     * Add an asset that should be loaded in the footer.
     *
     * @param \Concrete\Core\Asset\Asset $item
     */
    public function addFooterAsset($item)
    {
        $this->addOutputAssetAt($item, Asset::ASSET_POSITION_FOOTER);
    }

    /**
     * Add an asset at the position that's defined in the asset.
     *
     * @param \Concrete\Core\Asset\Asset $asset
     */
    public function addOutputAsset(Asset $asset)
    {
        $this->addOutputAssetAt($asset, $asset->getAssetPosition());
    }

    /**
     * Add an asset at a specific position.
     *
     * @param \Concrete\Core\Asset\Asset $item
     * @param string $position Whether the asset should be loaded in the header or in the footer.
     *
     * @see \Concrete\Core\Asset\AssetInterface::ASSET_POSITION_HEADER
     * @see \Concrete\Core\Asset\AssetInterface::ASSET_POSITION_FOOTER
     */
    protected function addOutputAssetAt($item, $position)
    {
        if (!isset($this->outputAssets[$position])) {
            $this->outputAssets[$position] = [$item];
        } elseif (!in_array($item, $this->outputAssets[$position])) {
            $this->outputAssets[$position][] = $item;
        }
    }

    /**
     * Get a list of assets that need to be outputted.
     *
     * Responsible for a number of things:
     * 1. Gets the required assets and adds them to the output assets array (which also contains other assets we have specifically asked for.)
     * 2. Returns the assets with the non-post-process-able assets FIRST, in the order in which they were added, with post-processable assets
     * grouped after. We also make sure to maintain the proper position.
     *
     * @return array[
     *  'H' => \Concrete\Core\Asset\Asset[]
     *  'F' => \Concrete\Core\Asset\Asset[]
     * ]
     */
    public function getAssetsToOutput()
    {
        $assets = $this->getRequiredAssetsToOutput();
        foreach ($assets as $asset) {
            $this->addOutputAsset($asset);
        }

        $outputAssetsPre = array();
        $outputAssets = array();

        // now we create temporary objects to store assets and their original key.
        // Why? Because not all "assets" in here are instances of the Asset class. Sometimes they're just strings.
        foreach ($this->outputAssets as $position => $assets) {
            $outputAssetsPre[$position] = array();
            foreach ($assets as $key => $asset) {
                $o = new \stdClass();
                $o->key = $key;
                $o->asset = $asset;
                $outputAssetsPre[$position][] = $o;
            }
        }

        // now we iterate through the $outputAssetsPre array, maintaining position, and sorting all the stdClass
        // objects within each array, keeping non-post-processed items first, and sorting by key.
        foreach ($outputAssetsPre as $position => $assets) {
            usort($assets, function ($o1, $o2) {
                $a1 = $o1->asset;
                $a2 = $o2->asset;
                $k1 = $o1->key;
                $k2 = $o2->key;

                // This is a great idea but it's not working. We're going to ditch this attempt at
                // intelligent grouping and just sort strictly by key.
                if ($k1 > $k2) {
                    return 1;
                } elseif ($k1 < $k2) {
                    return -1;
                } else {
                    return 0;
                }
                

            });

            foreach ($assets as $object) {
                $outputAssets[$position][] = $object->asset;
            }
        }

        return $outputAssets;
    }

    /**
     * Notes in the current request that a particular asset has already been provided.
     *
     * @param string $assetType E.g. 'css' or 'javascript'.
     * @param string|false $assetHandle E.g. 'core/colorpicker'.
     */
    public function markAssetAsIncluded($assetType, $assetHandle = false)
    {
        $list = AssetList::getInstance();
        if ($assetType && $assetHandle) {
            $asset = $list->getAsset($assetType, $assetHandle);
        } else {
            $assetGroup = $list->getAssetGroup($assetType);
        }

        if (isset($assetGroup)) {
            $this->providedAssetGroup->addGroup($assetGroup);
        } elseif (isset($asset)) {
            $ap = new AssetPointer($asset->getAssetType(), $asset->getAssetHandle());
            $this->providedAssetGroup->add($ap);
        } else {
            $ap = new AssetPointer($assetType, $assetHandle);
            $this->providedAssetGroupUnmatched[] = $ap;
        }
    }

    /**
     * Adds a required asset to this request. This asset will attempt to be output or included
     * when a view is rendered.
     *
     * @param \Concrete\Core\Asset\AssetGroup|\Concrete\Core\Asset\Asset|string $assetType
     * @param string|false $assetHandle used when $assetType is a string, to form the group $assetType/$assetHandle to define a new AssetPointer instance
     */
    public function requireAsset($assetType, $assetHandle = false)
    {
        $list = AssetList::getInstance();
        if ($assetType instanceof AssetGroup) {
            $this->requiredAssetGroup->addGroup($assetType);
        } elseif ($assetType instanceof Asset) {
            $this->requiredAssetGroup->addAsset($assetType);
        } elseif ($assetType && $assetHandle) {
            $ap = new AssetPointer($assetType, $assetHandle);
            $this->requiredAssetGroup->add($ap);
        } else {
            $r = $list->getAssetGroup($assetType);
            if (isset($r)) {
                $this->requiredAssetGroup->addGroup($r);
            } else {
                core_log(
                    t('ResponseAssetGroup::requireAsset required "%s" but "%s" is not a valid asset group handle', $assetType, $assetType),
                    Logger::NOTICE
                );
            }
        }
    }

    /**
     * Returns all required assets.
     *
     * @return \Concrete\Core\Asset\AssetGroup
     */
    public function getRequiredAssets()
    {
        return $this->requiredAssetGroup;
    }

    /**
     * @param \Concrete\Core\Asset\Asset $asset
     *
     * @return bool
     */
    protected function filterProvidedAssets($asset)
    {
        foreach ($this->providedAssetGroup->getAssetPointers() as $pass) {
            if ($pass->getHandle() == $asset->getHandle() && $pass->getType() == $asset->getType()) {
                return false;
            }
        }

        // now is the unmatched assets something that matches this asset?
        // (ie, is it a path-style match, like bootstrap/* )
        foreach ($this->providedAssetGroupUnmatched as $assetPointer) {
            if ($assetPointer->getType() == $asset->getType() && fnmatch($assetPointer->getHandle(), $asset->getHandle())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns only assets that are required but that aren't also in the providedAssetGroup.
     *
     * @return \Concrete\Core\Asset\Asset[]
     */
    public function getRequiredAssetsToOutput()
    {
        $required = $this->requiredAssetGroup->getAssetPointers();
        $assetPointers = array_filter($required, array('\Concrete\Core\Http\ResponseAssetGroup', 'filterProvidedAssets'));
        $assets = array();
        $al = AssetList::getInstance();
        foreach ($assetPointers as $ap) {
            $asset = $ap->getAsset();
            if ($asset instanceof Asset) {
                $assets[] = $asset;
            }
        }
        // also include any hard-passed $assets into the group. This is rare but it's used for handle-less
        // assets like layout css
        $assets = array_merge($this->requiredAssetGroup->getAssets(), $assets);

        return $assets;
    }
}
