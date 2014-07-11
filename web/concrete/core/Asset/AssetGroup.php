<?php
namespace Concrete\Core\Asset;

class AssetGroup
{
    protected $assetPointers = array();
    protected $assets = array();

    public function contains(AssetPointer $ap)
    {
        foreach ($this->assetPointers as $assetPointer) {
            if ($assetPointer->getHandle() == $ap->getHandle() && $assetPointer->getType() == $ap->getType()) {
                return true;
            }
        }

        return false;
    }

    public function addGroup(AssetGroup $item)
    {
        $assetPointers = $item->getAssetPointers();
        foreach ($assetPointers as $assetPointer) {
            if (!$this->contains($assetPointer)) {
                $this->assetPointers[] = $assetPointer;
            }
        }
    }

    public function addAsset(Asset $asset)
    {
        // doesn't check anything. this is useful for layouts, etc... other handle-less assets.
        $this->assets[] = $asset;
    }

    public function add(AssetPointer $ap)
    {
        if (!$this->contains($ap)) {
            $this->assetPointers[] = $ap;
        }
    }

    public function remove(AssetPointer $ap)
    {
        foreach ($this->assetPointers as $index => $assetPointer) {
            if ($assetPointer->getHandle() == $ap->getHandle() && $assetPointer->getType() == $ap->getType()) {
                array_splice($this->assetPointers, $index, 1);
                break;
            }
        }
    }

    public function getAssets()
    {
        return $this->assets;
    }

    public function getAssetPointers()
    {
        return $this->assetPointers;
    }

}
