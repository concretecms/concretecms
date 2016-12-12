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
            $str .= 'ccm_addHeaderItem("' . $asset->getAssetURL() . '", "CSS")';
            $str .= '</script>';
        } elseif ($asset instanceof JavascriptInlineAsset) {
            $str .= '<script type="text/javascript">';
            $str .= $asset->getAssetURL();
            $str .= '</script>';
        } elseif ($asset instanceof JavascriptAsset) {
            $str .= '<script type="text/javascript">';
            $str .= 'ccm_addHeaderItem("' . $asset->getAssetURL() . '", "JAVASCRIPT")';
            $str .= '</script>';
        }

        return $str . "\n";

    }
}