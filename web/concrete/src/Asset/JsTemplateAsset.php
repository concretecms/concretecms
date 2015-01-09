<?php
namespace Concrete\Core\Asset;

class JsTemplateAsset extends JavascriptAsset
{
    protected $assetSupportsMinification = false;
    protected $assetSupportsCombination = false;

    protected $scriptType = 'text/template';

    public function getAssetType()
    {
        return 'js-template';
    }

    public function getAssetDefaultPosition()
    {
        return Asset::ASSET_POSITION_HEADER;
    }


}
