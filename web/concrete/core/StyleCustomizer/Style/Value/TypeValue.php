<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;

class TypeValue extends Value
{
    protected $fontFamily = -1;
    protected $fontSize = -1;
    protected $color = -1;
    protected $lineHeight = -1;
    protected $letterSpacing = -1;
    protected $fontStyle = 'normal';
    protected $fontWeight = 'normal';
    protected $textDecoration = 'none';
    protected $textTransform = 'none';

    public function setFontFamily($fontFamily) {
        $this->fontFamily = $fontFamily;
    }

    public function setFontStyle($fontStyle)
    {
        $this->fontStyle = $fontStyle;
    }

    public function setFontSize(\Concrete\Core\StyleCustomizer\Style\Value\SizeValue $fontSize)
    {
        $this->fontSize = $fontSize;
    }

    public function setColor(\Concrete\Core\StyleCustomizer\Style\Value\ColorValue $color)
    {
        $this->color = $color;
    }

    public function setFontWeight($fontWeight)
    {
        $this->fontWeight = $fontWeight;
    }

    public function setTextDecoration($textDecoration)
    {
        $this->textDecoration = $textDecoration;
    }

    public function setTextTransform($textTransform)
    {
        $this->textTransform = $textTransform;
    }

    public function setLineHeight(\Concrete\Core\StyleCustomizer\Style\Value\SizeValue $lineHeight)
    {
        $this->lineHeight = $lineHeight;
    }

    public function setLetterSpacing(\Concrete\Core\StyleCustomizer\Style\Value\SizeValue $letterSpacing)
    {
        $this->letterSpacing = $letterSpacing;
    }

    public function getFontFamily()
    {
        return $this->fontFamily;
    }

    public function getFontSize()
    {
        return $this->fontSize;
    }

    public function getFontWeight()
    {
        return $this->fontWeight;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getTextDecoration()
    {
        return $this->textDecoration;
    }

    public function getTextTransform()
    {
        return $this->textTransform;
    }

    public function getFontStyle()
    {
        return $this->fontStyle;
    }

    public function getLetterSpacing()
    {
        return $this->letterSpacing;
    }

    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    public function toStyleString() {
        return '';
    }

    public function toLessVariablesArray()
    {

        $variables = array();
        $variables[$this->getVariable() . '-type-font-family'] = $this->getFontFamily();
        if (is_object($this->color)) {
            $variables[$this->getVariable() . '-type-color'] = $this->color->toStyleString();
        }
        $variables[$this->getVariable() . '-type-text-decoration'] = $this->getTextDecoration();
        $variables[$this->getVariable() . '-type-text-tranform'] = $this->getTextTransform();
        $variables[$this->getVariable() . '-type-font-style'] = $this->getFontStyle();
        $variables[$this->getVariable() . '-type-font-weight'] = $this->getFontWeight();
        if (is_object($this->fontSize) && $this->fontSize->getSize()) {
            $variables[$this->getVariable() . '-font-size'] = $this->fontSize->toStyleString();
        }
        if (is_object($this->lineHeight) && $this->lineHeight->getSize()) {
            $variables[$this->getVariable() . '-line-height'] = $this->lineHeight->toStyleString();
        }
        if (is_object($this->letterSpacing) && $this->letterSpacing->getSize()) {
            $variables[$this->getVariable() . '-letter-spacing'] = $this->letterSpacing->toStyleString();
        }
        return $variables;
    }

}