<?php
namespace Concrete\Core\Asset;

class JavascriptInlineAsset extends JavascriptAsset
{

    protected $assetSupportsMinification = false;
    protected $assetSupportsCombination = false;

    public function isAssetLocal()
    {
        return false;
    }

    public function getAssetDefaultPosition()
    {
        return Asset::ASSET_POSITION_FOOTER;
    }

    public function getAssetType()
    {
        return 'javascript-inline';
    }

    public function __toString()
    {
        $attrs = $this->getTagAttributeString();
        return '<script '. $attrs .' type="'. $this->scriptType . '">' . $this->getAssetURL() . '</script>';
    }

}
