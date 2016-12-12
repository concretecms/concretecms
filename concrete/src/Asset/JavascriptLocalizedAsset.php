<?php

namespace Concrete\Core\Asset;

use URL;
use Localization;

class JavascriptLocalizedAsset extends JavascriptAsset
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
        return 'javascript-localized';
    }

    public function getOutputAssetType()
    {
        return 'javascript';
    }

    public function getAssetURL()
    {
        return URL::to($this->assetURL)->getRelativeUrl();
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
