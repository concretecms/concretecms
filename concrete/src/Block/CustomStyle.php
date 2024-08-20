<?php
namespace Concrete\Core\Block;

use Concrete\Core\StyleCustomizer\Inline\CustomStyle as AbstractCustomStyle;
use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet;
use Core;

class CustomStyle extends AbstractCustomStyle
{
    protected $set;
    protected $theme;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var \Concrete\Core\Block\Block
     */
    public $block;

    public function __construct(?StyleSet $set, Block $b, $theme = null)
    {
        $this->block = $b;
        $this->set = $set;
        $this->theme = $theme;
    }

    public function getStyleWrapper($css)
    {
        $style = '<style type="text/css" data-area-style-area-handle="' . $this->block->getAreaHandle() . '" data-block-style-block-id="' . $this->block->getBlockID() . '" data-style-set="' . $this->getStyleSet()->getID() . '">' . $css . '</style>';

        return $style;
    }

    public function getCSS()
    {
        $set = $this->set;
        $groups = [];
        if ($set->getBackgroundColor()) {
            $groups[''][] = 'background-color:' . $set->getBackgroundColor();
        }
        $f = $set->getBackgroundImageFileObject();
        if (is_object($f)) {
            $url = $f->getRelativePath();
            if (!$url) {
                $url = $f->getURL();
            }
            $groups[''][] = 'background-image: url(' . $url . ')';
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
        if ($set->getBoxShadowColor()
        && ($set->getBoxShadowSpread()
        || $set->getBoxShadowBlur()
        || $set->getBoxShadowHorizontal()
        || $set->getBoxShadowVertical())) {
            $groups[''][] = 'box-shadow: ' .($set->getBoxShadowInset() ? 'inset ':'') . $set->getBoxShadowHorizontal() . ' ' . $set->getBoxShadowVertical() . ' ' . $set->getBoxShadowBlur() . ' ' . $set->getBoxShadowSpread() . ' ' . $set->getBoxShadowColor();
        }

        if ($set->getLinkColor()) {
            $groups[' a'][] = 'color:' . $set->getLinkColor() . ' !important';
        }

        $css = '';
        foreach ($groups as $suffix => $styles) {
            $css .= '.' . str_replace(' ', '.', $this->getCustomStyleClass()) . $suffix . '{' . implode(';', $styles) . '}';
        }

        return $css;
    }

    public function getCustomStyleClass()
    {
        $class = 'ccm-custom-style-container ccm-custom-style-';
        $txt = Core::make('helper/text');
        $class .= strtolower($txt->filterNonAlphaNum($this->block->getAreaHandle()));
        $class .= '-' . $this->block->getBlockID();

        return $class;
    }

    public function getContainerClass()
    {
        $classes = [$this->getCustomStyleClass()];
        if ($this->block->getBlockFilename()) {
            $template = $this->block->getBlockFilename();
            $template = str_replace('.php', '', $template);
            $template = str_replace('_', '-', $template);
            $classes[] = 'ccm-block-custom-template-' . $template;
        }
        if (is_object($this->set)) {
            if ($this->set->getCustomClass()) {
                $classes[] = $this->set->getCustomClass();
            }
            if (is_object($this->theme) && ($gf = $this->theme->getThemeGridFrameworkObject())) {
                $classes = array_merge($gf->getPageThemeGridFrameworkSelectedDeviceHideClassesForDisplay($this->set, $this->block->getBlockCollectionObject()), $classes);
            }
        }

        return implode(' ', $classes);
    }

    public function getCustomStyleID()
    {
        $id = null;
        if (is_object($this->set)) {
            if ($this->set->getCustomID()) {
                $id = $this->set->getCustomID();
            }
        }

        return $id;
    }

    public function getCustomStyleElementAttribute()
    {
        $elementAttribute = null;
        if (is_object($this->set)) {
            if ($this->set->getCustomElementAttribute()) {
                $elementAttribute = $this->set->getCustomElementAttribute();
            }
        }

        return $elementAttribute;
    }
}
