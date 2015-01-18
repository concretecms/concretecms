<?php
namespace Concrete\Core\Asset;

class AssetPointer
{

    /**
     * @var string
     */
    protected $assetType;

    /**
     * @var string
     */
    protected $assetHandle;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->assetType;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->assetHandle;
    }

    /**
     * @param string $assetType
     * @param string $assetHandle
     */
    public function __construct($assetType, $assetHandle)
    {
        $this->assetType = $assetType;
        $this->assetHandle = $assetHandle;
    }

    /**
     * @return Asset
     */
    public function getAsset()
    {
        $al = AssetList::getInstance();
        return $al->getAsset($this->assetType, $this->assetHandle);
    }
}
