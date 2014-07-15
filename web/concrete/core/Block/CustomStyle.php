<?
namespace Concrete\Core\Block;
use Concrete\Core\StyleCustomizer\Inline\CustomStyleInterface;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Core;

class CustomStyle implements CustomStyleInterface
{

    protected $arHandle;
    protected $bID;
    protected $set;

    public function __construct(StyleSet $set, $bID, $arHandle)
    {
        $this->arHandle = $arHandle;
        $this->bID = $bID;
        $this->set = $set;
    }

    public function getStyleSet()
    {
        return $this->set;
    }

    public function getCSS()
    {
        $set = $this->set;
        $css = '.' . $this->getContainerClass() . '{';
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
        if ($set->getMarginTop()) {
            $css .= 'margin-top:' . $set->getMarginTop() . ';';
        }
        if ($set->getMarginRight()) {
            $css .= 'margin-right:' . $set->getMarginRight() . ';';
        }
        if ($set->getMarginBottom()) {
            $css .= 'margin-bottom:' . $set->getMarginBottom() . ';';
        }
        if ($set->getMarginLeft()) {
            $css .= 'margin-left:' . $set->getMarginLeft() . ';';
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

        $css .= '}';

        if ($set->getLinkColor()) {
            $css .= '.' . $this->getContainerClass() . ' a {';
            $css .= 'color:' . $set->getLinkColor() . ' !important;';
            $css .= '}';
        }
        return $css;
    }

    public function getContainerClass()
    {
        $class = 'ccm-custom-style-';
        $txt = Core::make('helper/text');
        $class .= strtolower($txt->filterNonAlphaNum($this->arHandle));
        $class .= '-' . $this->bID;
        return $class;
    }
}