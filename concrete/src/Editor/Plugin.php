<?php

namespace Concrete\Core\Editor;

use AssetList;
use Concrete\Core\Asset\AssetGroup;
use Concrete\Core\Asset\AssetInterface;
use Concrete\Core\Asset\AssetPointer;
use Exception;

class Plugin
{
    /**
     * The plugin key.
     *
     * @var string
     */
    protected $key;

    /**
     * The plugin name.
     *
     * @var string
     */
    protected $name;

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The list of required assets for this plugin.
     *
     * @var \Concrete\Core\Asset\AssetGroup
     */
    protected $requiredAssetGroup;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this->requiredAssetGroup = new AssetGroup();
    }

    /**
     * Get the list of required assets for this plugin.
     *
     * @return \Concrete\Core\Asset\AssetGroup
     */
    public function getRequiredAssets()
    {
        return $this->requiredAssetGroup;
    }

    /**
     * Add an asset to the assets required for this plugin.
     *
     * @param \Concrete\Core\Asset\AssetInterface|string $assetType The asset to require, or the asset group handle, or the asset type (in this case, specify the $assetHandle parameter)
     * @param string|null|false $assetHandle the handle of the asset to specify (if $assetType is the asset type handle)
     *
     * @throws \Exception throws an Exception if the asset is not valid
     */
    public function requireAsset($assetType, $assetHandle = false)
    {
        $list = AssetList::getInstance();
        if ($assetType instanceof AssetInterface) {
            $this->requiredAssetGroup->addAsset($assetType);
        } elseif ($assetType && $assetHandle) {
            $ap = new AssetPointer($assetType, $assetHandle);
            $this->requiredAssetGroup->add($ap);
        } else {
            $r = $list->getAssetGroup($assetType);
            if (isset($r)) {
                $this->requiredAssetGroup->addGroup($r);
            } else {
                throw new Exception(t('"%s" is not a valid asset group handle', $assetType));
            }
        }
    }

    /**
     * Get the plugin key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the plugin key.
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get the plugin name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the plugin name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the plugin description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the plugin description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
    }
}
