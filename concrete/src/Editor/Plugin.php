<?php
namespace Concrete\Core\Editor;

use Concrete\Core\Asset\AssetGroup;
use Concrete\Core\Asset\AssetPointer;

class Plugin
{

    protected $key;
    protected $name;
    protected $requiredAssetGroup;

    public function __construct()
    {
        $this->requiredAssetGroup = new AssetGroup();
    }

    public function getRequiredAssets()
    {
        return $this->requiredAssetGroup;
    }

    public function requireAsset($assetType, $assetHandle = false)
    {
        $list = \AssetList::getInstance();
        if ($assetType instanceof Asset) {
            $this->requiredAssetGroup->addAsset($assetType);
        } elseif ($assetType && $assetHandle) {
            $ap = new AssetPointer($assetType, $assetHandle);
            $this->requiredAssetGroup->add($ap);
        } else {
            $r = $list->getAssetGroup($assetType);
            if (isset($r)) {
                $this->requiredAssetGroup->addGroup($r);
            } else {
                throw new \Exception(t('"%s" is not a valid asset group handle', $assetType));
            }
        }
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}