<?php
/**
 * Created by PhpStorm.
 * User: Korvin
 * Date: 7/26/15
 * Time: 6:49 PM
 */

namespace Concrete\Core\ImageEditor;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\View\View;

class ExtensionFactory
{

    /**
     * @var AssetList
     */
    protected $assetList;

    public function __construct(AssetList $asset_list)
    {
        $this->assetList = $asset_list;
    }

    /**
     * @param array $config
     * @return Extension
     */
    public function extensionFromConfig(array $config)
    {
        $extension = new Extension();

        $extension->setName(array_get($config, 'name'));
        $extension->setHandle(array_get($config, 'handle'));

        $asset = $this->assetList->getAsset('javascript', array_get($config, 'src'));
        if (!$asset) {
            $handle = array_get($config, 'handle');
            throw new \RuntimeException("Could not build extension \"{$handle}\", invalid extension asset.");
        } else {
            $extension->setExtensionAsset($asset);
        }

        $view = new View(array_get($config, 'view'));
        $extension->setView($view);

        $assets = (array)array_get($config, 'assets');
        foreach ($assets as $handle => $asset_config) {
            $asset_config = (array)$asset_config;

            foreach ($asset_config as $type) {
                if ($asset = $this->assetList->getAsset($type, $handle)) {
                    $extension->addAsset($asset);
                } else {
                    $handle = array_get($config, 'handle');
                    throw new \RuntimeException("Could not build extension \"{$handle}\", invalid asset.");
                }
            }
        }

        return $extension;
    }

}
