<?php

namespace Concrete\Core\Asset;

class JavascriptImportmapAsset extends JavascriptAsset
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
     * @return string
     */
    public function __toString()
    {
        return '<script type="importmap">' . $this->getAssetURL() . '</script>';
    }

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
        return 'javascript-importmap';
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
     * @return string|null
     */
    public function getAssetContents()
    {
        return $this->assetURL;
    }
}
