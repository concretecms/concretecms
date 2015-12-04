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
    protected $theme;
    protected $bFilename;

    public function __construct(StyleSet $set = null, Block $b, $theme = null)
    {
        $this->arHandle = $b->getAreaHandle();
        $this->bID = $b->getBlockID();
        $this->set = $set;
        $this->theme = $theme;
        $this->bFilename = $b->getBlockFilename();
    }

    public function getStyleWrapper($css)
    {
        $style = '<style type="text/css" data-area-style-area-handle="' . $this->arHandle . '" data-block-style-block-id="' . $this->bID . '" data-style-set="' . $this->getStyleSet()->getID() . '">' . $css . '</style>';
        return $style;
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
            $groups[''][] = 'background-size: ' . $set->getBackgroundSize();
            $groups[''][] = 'background-position: ' . $set->getBackgroundPosition();
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
        if (isset($this->bFilename) && $this->bFilename) {
            $template = $this->bFilename;
            $template = str_replace('.php', '', $template);
            $template = str_replace('_', '-', $template);
            $class .= ' ccm-block-custom-template-' . $template;
        }
        if (is_object($this->set)) {
            $return = $this->set->getClass($this->theme);
            if ($return) {
                $class .= ' ' . $return;
            }
        }
        return $class;
    }
}
