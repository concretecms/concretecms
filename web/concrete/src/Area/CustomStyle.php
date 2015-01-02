<?php
namespace Concrete\Core\Area;
use Concrete\Core\StyleCustomizer\Inline\CustomStyle as AbstractCustomStyle;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Core;

class CustomStyle extends AbstractCustomStyle
{

    protected $arHandle;
    protected $set;

    public function __construct(StyleSet $set = null, $arHandle)
    {
        $this->arHandle = $arHandle;
        $this->set = $set;
    }

    public function getCSS()
    {
        $set = $this->set;
        $css = '.' . $this->getContainerClass('.') . '{';
        if ($set->getBackgroundColor()) {
            $css .= 'background-color:' . $set->getBackgroundColor() . ';';
        }
        $f = $set->getBackgroundImageFileObject();
        if (is_object($f)) {
            $css .= 'background-image: url(' . $f->getRelativePath() . ');';
            $css .= 'background-repeat: ' . $set->getBackgroundRepeat() . ';';
        }
        if ($set->getBaseFontSize()) {
            $css .= 'font-size:' . $set->getBaseFontSize() . ';';
        }
        if ($set->getTextColor()) {
            $css .= 'color:' . $set->getTextColor() . ';';
        }
        if ($set->getPaddingTop()) {
            $css .= 'padding-top:' . $set->getPaddingTop() . ';';
        }
        if ($set->getPaddingRight()) {
            $css .= 'padding-right:' . $set->getPaddingRight() . ';';
        }
        if ($set->getPaddingBottom()) {
            $css .= 'padding-bottom:' . $set->getPaddingBottom() . ';';
        }
        if ($set->getPaddingLeft()) {
            $css .= 'padding-left:' . $set->getPaddingLeft() . ';';
        }
        if ($set->getBorderWidth()) {
            $css .= 'border-width:' . $set->getBorderWidth() . ';';
        }
        if ($set->getBorderStyle()) {
            $css .= 'border-style:' . $set->getBorderStyle() . ';';
        }
        if ($set->getBorderColor()) {
            $css .= 'border-color:' . $set->getBorderColor() . ';';
        }
        if ($set->getAlignment()) {
            $css .= 'text-align:' . $set->getAlignment() . ';';
        }
        if ($set->getBorderRadius()) {
            $css .= 'border-radius:' . $set->getBorderRadius() . ';';
            $css .= '-moz-border-radius:' . $set->getBorderRadius() . ';';
            $css .= '-webkit-border-radius:' . $set->getBorderRadius() . ';';
            $css .= '-o-border-radius:' . $set->getBorderRadius() . ';';
            $css .= '-ms-border-radius:' . $set->getBorderRadius() . ';';
        }
        if ($set->getRotate()) {
            $css .= 'transform: rotate(' . $set->getRotate() . 'deg);';
            $css .= '-moz-transform: rotate(' . $set->getRotate() . 'deg);';
            $css .= '-webkit-transform: rotate(' . $set->getRotate() . 'deg);';
            $css .= '-o-transform: rotate(' . $set->getRotate() . 'deg);';
            $css .= '-ms-transform: rotate(' . $set->getRotate() . 'deg);';
        }
        if ($set->getBoxShadowSpread()) {
            $css .= 'box-shadow: ' . $set->getBoxShadowHorizontal() . ' ' . $set->getBoxShadowVertical();
            $css .= ' ' . $set->getBoxShadowBlur() . ' ' . $set->getBoxShadowSpread() . ' ' . $set->getBoxShadowColor();
        }

        $css .= '}';

        if ($set->getLinkColor()) {
            $css .= '.' . $this->getContainerClass() . ' a {';
            $css .= 'color:' . $set->getLinkColor() . ' !important;';
            $css .= '}';
        }
        return $css;
    }

    public function getContainerClass($separator = ' ')
    {
        $class = 'ccm-custom-style-';
        $txt = Core::make('helper/text');
        $class .= strtolower($txt->filterNonAlphaNum($this->arHandle));
        if (is_object($this->set) && $this->set->getCustomClass()) {
            $class .= $separator . $this->set->getCustomClass();
        }
        return $class;
    }
}
