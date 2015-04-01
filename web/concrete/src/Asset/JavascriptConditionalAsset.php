<?php
namespace Concrete\Core\Asset;

class JavascriptConditionalAsset extends JavascriptAsset
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

    /**
     * @return string
     */
    public function __toString()
    {
        $string = parent::__toString();
        if (!$this->conditional) {
            return $string;
        } else {
            return sprintf('<!--[if %s]>%s<![endif]-->', $this->conditional, $string);
        }
    }

    public function register($filename, $args, $pkg = false)
    {
        parent::register($filename, $args, $pkg);
        $this->conditional = $args['conditional'];
    }

}
