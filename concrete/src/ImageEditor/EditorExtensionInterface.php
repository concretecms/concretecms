<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\View\AbstractView;

/**
 * @since 5.7.5
 */
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
