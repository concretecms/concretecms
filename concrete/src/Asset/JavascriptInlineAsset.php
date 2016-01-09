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
    public function getAssetType()
    {
        return 'javascript-inline';
    }

    public function getOutputAssetType()
    {
        return 'javascript';
    }

    /**
     * @return string
     */
    public function getAssetHashKey()
    {
        return md5($this->assetURL);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '<script type="text/javascript">' . $this->getAssetURL() . '</script>';
    }

    /**
     * @return string|null
     */
    public function getAssetContents()
    {
        return $this->assetURL;
    }

}
