<?php
namespace Concrete\Core\Asset;

class CssInlineAsset extends CssAsset
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
        return 'css-inline';
    }

    public function getOutputAssetType()
    {
        return 'css';
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
        return '<style type="text/css">'.$this->getAssetURL().'</style>';
    }

    /**
     * @return string|null
     */
    public function getAssetContents()
    {
        return $this->assetURL;
    }
}
