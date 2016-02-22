<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\Asset\AssetInterface;
use Concrete\Core\View\AbstractView;

class Extension implements EditorExtensionInterface
{
    /** @var array */
    protected $assets = array();

    /** @var string */
    protected $handle;

    /** @var string */
    protected $name;

    /** @var AssetInterface */
    protected $extensionAsset;

    /** @var AbstractView */
    protected $view;

    /**
     * @param AssetInterface $asset
     */
    public function addAsset(AssetInterface $asset)
    {
        $this->assets[] = $asset;
    }

    /**
     * @param array $assets
     */
    public function setAssets(array $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param AssetInterface $asset
     */
    public function setExtensionAsset($asset)
    {
        $this->extensionAsset = $asset;
    }

    /**
     * @param AbstractView $view
     */
    public function setView(AbstractView $view)
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return AssetInterface
     */
    public function getExtensionAsset()
    {
        return $this->extensionAsset;
    }

    /**
     * @return \Concrete\Core\Asset\AssetInterface[]
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @return AbstractView
     */
    public function getView()
    {
        return $this->view;
    }
}
