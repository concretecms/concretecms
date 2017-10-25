<?php
namespace Concrete\Core\Asset\Output;

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Asset\JavascriptAsset;
use Concrete\Core\Asset\JavascriptInlineAsset;

class JavascriptFormatter implements FormatterInterface
{

    public function output(Asset $asset)
    {
        $str = '';
        if ($asset instanceof CssAsset) {
            $str .= '<script type="text/javascript">';
            $str .= 'ConcreteAssetLoader.loadCSS("' . $asset->getAssetURL() . '")';
            $str .= '</script>';
        } elseif ($asset instanceof JavascriptInlineAsset) {
            $str .= '<script type="text/javascript">';
            $str .= $asset->getAssetURL();
            $str .= '</script>';
        } elseif ($asset instanceof JavascriptAsset) {
            $str .= '<script type="text/javascript">';
            $str .= 'ConcreteAssetLoader.loadJavaScript("' . $asset->getAssetURL() . '")';
            $str .= '</script>';
        }

        return $str . "\n";

    }
}