<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;

class TypeValue extends Value
{
    protected $fontFamily = -1;
    protected $fontSize = -1;
    protected $color = -1;
    protected $lineHeight = -1;
    protected $letterSpacing = -1;
    protected $fontWeight = 'normal';
    protected $textDecoration = 'none';
    protected $textTransform = 'none';

    public function setFontFamily($fontFamily) {
        $this->fontFamily = $fontFamily;
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

    public function getLetterSpacing()
    {
        return $this->letterSpacing;
    }

    public function getLineHeight()
    {
        return $this->lineHeight;
    }

}