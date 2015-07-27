<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\View\AbstractView;

interface EditorExtensionInterface
{

    /**
     * @return string
     */
    public function getHandle();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return \Concrete\Core\Asset\AssetInterface
     */
    public function getExtensionAsset();

    /**
     * @return \Concrete\Core\Asset\AssetInterface[]
     */
    public function getAssets();

    /**
     * @return AbstractView
     */
    public function getView();

}
