<?php
namespace Concrete\Core\Asset;

class AssetGroup
{
    /**
     * @var \Concrete\Core\Asset\AssetPointer[]
     */
    protected $assetPointers = array();

    /**
     * @var Asset[]
     */
    protected $assets = array();

    /**
     * @param AssetPointer $ap
     * @return bool
     */
    public function contains(AssetPointer $ap)
    {
        foreach ($this->assetPointers as $assetPointer) {
            if ($assetPointer->getHandle() == $ap->getHandle() && $assetPointer->getType() == $ap->getType()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AssetGroup $item
     */
    public function addGroup(AssetGroup $item)
    {
        $assetPointers = $item->getAssetPointers();
        foreach ($assetPointers as $assetPointer) {
            if (!$this->contains($assetPointer)) {
                $this->assetPointers[] = $assetPointer;
            }
        }
    }

    /**
     * @param Asset $asset
     */
    public function addAsset(Asset $asset)
    {
        // doesn't check anything. this is useful for layouts, etc... other handle-less assets.
        $this->assets[] = $asset;
    }


    /**
     * @param AssetPointer $ap
     */
    public function add(AssetPointer $ap)
    {
        if (!$this->contains($ap)) {
            $this->assetPointers[] = $ap;
        }
    }

    /**
     * @return Asset[]
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @return AssetPointer[]
     */
    public function getAssetPointers()
    {
        return $this->assetPointers;
    }
}
