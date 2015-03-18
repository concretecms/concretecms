<?php
namespace Concrete\Core\Asset;

class JavascriptInlineAsset extends JavascriptAsset
{

    /**
     * @var bool
     */
    protected $assetSupportsMinification = false;

    /**
     * @var bool
     */
    protected $assetSupportsCombination = false;

    /**
     * @return bool
     */
    public function isAssetLocal()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getAssetDefaultPosition()
    {
        return Asset::ASSET_POSITION_FOOTER;
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return 'javascript-inline';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '<script type="text/javascript">' . $this->getAssetURL() . '</script>';
    }
}
