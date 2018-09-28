<?php
namespace Concrete\Core\Asset;

use Concrete\Core\Foundation\ConcreteObject;

class AssetList
{
    /**
     * @var null|self
     */
    private static $loc = null;

    /**
     * @var array Array of assets with type, version, and handle
     */
    public $assets = array();

    /**
     * @var AssetGroup[] map<handle, AssetGroup>
     */
    public $assetGroups = array();

    public function getRegisteredAssets()
    {
        return $this->assets;
    }

    /**
     * @return \Concrete\Core\Asset\AssetGroup[]
     */
    public function getRegisteredAssetGroups()
    {
        return $this->assetGroups;
    }

    /**
     * @return AssetList
     */
    public static function getInstance()
    {
        if (null === self::$loc) {
            self::$loc = new self();
        }

        return self::$loc;
    }

    /**
     * @param string $assetType
     * @param string $assetHandle
     * @param string $filename
     * @param array $args
     * @param bool $pkg
     *
     * @return Asset
     */
    public function register($assetType, $assetHandle, $filename, $args = array(), $pkg = false)
    {
        $defaults = array(
            'position' => false,
            'local' => true,
            'version' => false,
            'combine' => -1,
            'minify' => -1, // use the asset default
        );
        // overwrite all the defaults with the arguments
        $args = array_merge($defaults, $args);

        $class = '\\Concrete\\Core\\Asset\\' . ConcreteObject::camelcase($assetType) . 'Asset';
        $o = new $class($assetHandle);
        $o->register($filename, $args, $pkg);
        $this->registerAsset($o);

        return $o;
    }

    /**
     * @param array $assets
     */
    public function registerMultiple(array $assets)
    {
        foreach ($assets as $asset_handle => $asset_types) {
            foreach ($asset_types as $asset_type => $asset_settings) {
                array_splice($asset_settings, 1, 0, $asset_handle);
                call_user_func_array(array($this, 'register'), $asset_settings);
            }
        }
    }

    /**
     * @param Asset $asset
     */
    public function registerAsset(Asset $asset)
    {
        // we have to check and see if the asset already exists.
        // If it exists, we only replace it if our current asset has a later version
        $doRegister = true;
        if (isset($this->assets[$asset->getAssetType()][$asset->getAssetHandle()])) {
            $existingAsset = $this->assets[$asset->getAssetType()][$asset->getAssetHandle()];
            if (version_compare($existingAsset->getAssetVersion(), $asset->getAssetVersion(), '>')) {
                $doRegister = false;
            }
        }
        if ($doRegister) {
            $this->assets[$asset->getAssetType()][$asset->getAssetHandle()] = $asset;
        }
    }

    /**
     * @param string $assetGroupHandle
     * @param array $assetHandles
     * @param bool $customClass
     */
    public function registerGroup($assetGroupHandle, $assetHandles, $customClass = false)
    {
        if ($customClass) {
            $class = '\\Concrete\\Core\\Asset\\Group\\' . ConcreteObject::camelcase($assetGroupHandle) . 'AssetGroup';
        } else {
            $class = '\\Concrete\\Core\\Asset\\AssetGroup';
        }
        $group = new $class();
        foreach ($assetHandles as $assetArray) {
            $ap = new AssetPointer($assetArray[0], $assetArray[1]);
            $group->add($ap);
        }
        $this->assetGroups[$assetGroupHandle] = $group;
    }

    /**
     * @param array $asset_groups
     */
    public function registerGroupMultiple(array $asset_groups)
    {
        foreach ($asset_groups as $group_handle => $group_setting) {
            array_unshift($group_setting, $group_handle);
            call_user_func_array(array($this, 'registerGroup'), $group_setting);
        }
    }

    /**
     * @param string $assetType
     * @param string $assetHandle
     *
     * @return Asset
     */
    public function getAsset($assetType, $assetHandle)
    {
        return isset($this->assets[$assetType][$assetHandle]) ? $this->assets[$assetType][$assetHandle] : null;
    }

    /**
     * @param string $assetGroupHandle
     *
     * @return \Concrete\Core\Asset\AssetGroup
     */
    public function getAssetGroup($assetGroupHandle)
    {
        return $this->assetGroups[$assetGroupHandle];
    }
}
