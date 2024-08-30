<?php

namespace Concrete\Core\Asset;

use HtmlObject\Element;

class JavascriptModuleAsset extends JavascriptAsset
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
        $e = new Element('script');
        $e->type('module')->src($this->getAssetURL());

        return (string) $e;
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return 'javascript-module';
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
}
