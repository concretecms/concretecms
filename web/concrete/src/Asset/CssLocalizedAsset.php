<?php

namespace Concrete\Core\Asset;

use URL;
use Localization;

class CssLocalizedAsset extends CssAsset
{
    /**
     * @var bool
     */
    protected $assetSupportsMinification = false;

    /**
     * @return string
     */
    public function getAssetType()
    {
        return 'css-localized';
    }

    public function getOutputAssetType()
    {
        return 'css';
    }

    public function getAssetURL()
    {
        return (string) URL::to($this->assetURL);
    }

    /**
     * @return string
     */
    public function getAssetHashKey()
    {
        return $this->assetURL.'::'.Localization::activeLocale().'::'.sha1($this->getAssetContents());
    }

    public function isAssetLocal()
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getAssetContents()
    {
        return parent::getAssetContentsByRoute($this->assetURL);
    }
}
