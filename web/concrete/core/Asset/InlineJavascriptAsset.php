<?php
namespace Concrete\Core\Asset;

class InlineJavascriptAsset extends Asset
{
    protected $assetSupportsMinification = false;
    protected $assetSupportsCombination = false;

    protected $code = '';
    public function setCode($code)
    {
        $this->code = $code;
    }
    public function getCode()
    {
        return $this->code;
    }

    public function getAssetType()
    {
        return 'inline_javascript';
    }

    public function getAssetDefaultPosition()
    {
        return null;
    }

    public function combine($assets) { }

    public function minify($assets) { }

    public function __toString()
    {
        return "<script type=\"text/javascript\">\n{$this->code}\n</script>";
    }

}
