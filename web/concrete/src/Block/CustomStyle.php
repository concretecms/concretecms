<?php
namespace Concrete\Core\Block;
use Concrete\Core\StyleCustomizer\Inline\CustomStyle as AbstractCustomStyle;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Core;

class CustomStyle extends AbstractCustomStyle
{

    protected $arHandle;
    protected $bID;
    protected $set;

    public function __construct(StyleSet $set = null, $bID, $arHandle)
    {
        $this->arHandle = $arHandle;
        $this->bID = $bID;
        $this->set = $set;
    }

    public function getCSS()
    {
        $set = $this->set;
        $groups = array();
        if ($set->getBackgroundColor()) {
            $groups[''][] = 'background-color:' . $set->getBackgroundColor();
        }
        $f = $set->getBackgroundImageFileObject();
        if (is_object($f)) {
            $groups[''][] = 'background-image: url(' . $f->getRelativePath() . ')';
            $groups[''][] = 'background-repeat: ' . $set->getBackgroundRepeat();
        }
        if ($set->getBaseFontSize()) {
            $groups[''][] = 'font-size:' . $set->getBaseFontSize();
        }
        if ($set->getTextColor()) {
            $groups[''][] = 'color:' . $set->getTextColor();
        }
        if ($set->getMarginTop()) {
            $groups[''][] = 'margin-top:' . $set->getMarginTop();
        }
        if ($set->getMarginRight()) {
            $groups[''][] = 'margin-right:' . $set->getMarginRight();
        }
        if ($set->getMarginBottom()) {
            $groups[''][] = 'margin-bottom:' . $set->getMarginBottom();
        }
        if ($set->getMarginLeft()) {
            $groups[''][] = 'margin-left:' . $set->getMarginLeft();
        }
        if ($set->getPaddingTop()) {
            $groups[''][] = 'padding-top:' . $set->getPaddingTop();
        }
        if ($set->getPaddingRight()) {
            $groups[''][] = 'padding-right:' . $set->getPaddingRight();
        }
        if ($set->getPaddingBottom()) {
            $groups[''][] = 'padding-bottom:' . $set->getPaddingBottom();
        }
        if ($set->getPaddingLeft()) {
            $groups[''][] = 'padding-left:' . $set->getPaddingLeft();
        }
        if ($set->getBorderWidth()) {
            $groups[''][] = 'border-width:' . $set->getBorderWidth();
        }
        if ($set->getBorderStyle()) {
            $groups[''][] = 'border-style:' . $set->getBorderStyle();
        }
        if ($set->getBorderColor()) {
            $groups[''][] = 'border-color:' . $set->getBorderColor();
        }
        if ($set->getAlignment()) {
            $groups[''][] = 'text-align:' . $set->getAlignment();
        }
        if ($set->getBorderRadius()) {
            $groups[''][] = 'border-radius:' . $set->getBorderRadius();
            $groups[''][] = '-moz-border-radius:' . $set->getBorderRadius();
            $groups[''][] = '-webkit-border-radius:' . $set->getBorderRadius();
            $groups[''][] = '-o-border-radius:' . $set->getBorderRadius();
            $groups[''][] = '-ms-border-radius:' . $set->getBorderRadius();
        }
        if ($set->getRotate()) {
            $groups[''][] = 'transform: rotate(' . $set->getRotate() . 'deg)';
            $groups[''][] = '-moz-transform: rotate(' . $set->getRotate() . 'deg)';
            $groups[''][] = '-webkit-transform: rotate(' . $set->getRotate() . 'deg)';
            $groups[''][] = '-o-transform: rotate(' . $set->getRotate() . 'deg)';
            $groups[''][] = '-ms-transform: rotate(' . $set->getRotate() . 'deg)';
        }
        if ($set->getBoxShadowSpread()) {
            $groups[''][] = 'box-shadow: ' . $set->getBoxShadowHorizontal() . ' ' . $set->getBoxShadowVertical() . ' ' . $set->getBoxShadowBlur() . ' ' . $set->getBoxShadowSpread() . ' ' . $set->getBoxShadowColor();
        }

        if ($set->getLinkColor()) {
            $groups[' a'][] = 'color:' . $set->getLinkColor() . ' !important';
        }

        $css = '';
        foreach($groups as $suffix => $styles) {
            $css .= '.' . str_replace(' ', '.', $this->getContainerClass()) . $suffix . '{'.implode(';', $styles).'}';
        }
        return $css;
    }

    public function getContainerClass()
    {
        $class = 'ccm-custom-style-container ccm-custom-style-';
        $txt = Core::make('helper/text');
        $class .= strtolower($txt->filterNonAlphaNum($this->arHandle));
        $class .= '-' . $this->bID;
        if (is_object($this->set) && $this->set->getCustomClass()) {
            $class .= ' ' . $this->set->getCustomClass();
        }
        return $class;
    }
}
