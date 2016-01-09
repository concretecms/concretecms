<?php
namespace Concrete\Core\Asset;

use HtmlObject\Element;

class JavascriptConditionalAsset extends Asset
{

    protected $conditional = null;

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
    public function getAssetType()
    {
        return 'javascript-conditional';
    }

    public function getOutputAssetType()
    {
        return 'javascript';
    }

    public static function process($assets)
    {
        return $assets;
    }

    /**
     * @return string
     */
    public function getAssetDefaultPosition()
    {
        return Asset::ASSET_POSITION_HEADER;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $e = new Element('script');
        $e->type('text/javascript')->src($this->getAssetURL());

        if (!$this->conditional) {
            return (string) $e;
        } else {
            return sprintf('<!--[if %s]>%s<![endif]-->', $this->conditional, (string) $e);
        }
    }

    public function register($filename, $args, $pkg = false)
    {
        parent::register($filename, $args, $pkg);
        $this->conditional = $args['conditional'];
    }

}
