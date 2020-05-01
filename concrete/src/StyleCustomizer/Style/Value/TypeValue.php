<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class TypeValue extends Value
{
    /**
     * The font family.
     *
     * @var string|-1
     *
     * @example 'Verdana'
     */
    protected $fontFamily = -1;

    /**
     * The font size.
     *
     * @var \Concrete\Core\StyleCustomizer\Style\Value\SizeValue|-1
     */
    protected $fontSize = -1;

    /**
     * The font color.
     *
     * @var \Concrete\Core\StyleCustomizer\Style\Value\ColorValue|-1
     */
    protected $color = -1;

    /**
     * The line height.
     *
     * @var \Concrete\Core\StyleCustomizer\Style\Value\SizeValue|-1
     */
    protected $lineHeight = -1;

    /**
     * The letter spacing.
     *
     * @var \Concrete\Core\StyleCustomizer\Style\Value\SizeValue|-1
     */
    protected $letterSpacing = -1;

    /**
     * The font style.
     *
     * @var string|-1
     *
     * @example 'italic'
     * @example 'none'
     */
    protected $fontStyle = -1;

    /**
     * The font weight.
     *
     * @var string|-1
     */
    protected $fontWeight = -1;

    /**
     * The text decoration.
     *
     * @var string|-1
     *
     * @example 'underline'
     * @example 'none'
     */
    protected $textDecoration = -1;

    /**
     * The text transform.
     *
     * @var string|-1
     *
     * @example 'uppercase'
     * @example 'none'
     */
    protected $textTransform = -1;

    /**
     * Set the font family.
     *
     * @param string $fontFamily
     *
     * @return $this
     *
     * @example 'Verdana'
     */
    public function setFontFamily($fontFamily)
    {
        $this->fontFamily = $fontFamily == -1 ? -1 : (string) $fontFamily;

        return $this;
    }

    /**
     * Set the font style.
     *
     * @var string
     *
     * @example 'italic'
     * @example 'none'
     *
     * @param mixed $fontStyle
     *
     * @return $this
     */
    public function setFontStyle($fontStyle)
    {
        $this->fontStyle = $fontStyle == -1 ? -1 : (string) $fontStyle;

        return $this;
    }

    /**
     * Set the font size.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\SizeValue $fontSize
     *
     * @return $this
     */
    public function setFontSize(SizeValue $fontSize)
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    /**
     * Set the font color.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\ColorValue $color
     *
     * @return $this
     */
    public function setColor(ColorValue $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Set the font weight.
     *
     * @param string $fontWeight
     *
     * @return $this
     */
    public function setFontWeight($fontWeight)
    {
        $this->fontWeight = $fontWeight == -1 ? -1 : (string) $fontWeight;

        return $this;
    }

    /**
     * Set the text decoration.
     *
     * @param string $textDecoration
     *
     * @return $this
     *
     * @example 'underline'
     * @example 'none'
     */
    public function setTextDecoration($textDecoration)
    {
        $this->textDecoration = $textDecoration == -1 ? -1 : (string) $textDecoration;

        return $this;
    }

    /**
     * Set the text transform.
     *
     * @param string $textTransform
     *
     * @return $this
     *
     * @example 'uppercase'
     * @example 'none'
     */
    public function setTextTransform($textTransform)
    {
        $this->textTransform = $textTransform == -1 ? -1 : (string) $textTransform;

        return $this;
    }

    /**
     * Set the line height.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\SizeValue $lineHeight
     *
     * @return $this
     */
    public function setLineHeight(SizeValue $lineHeight)
    {
        $this->lineHeight = $lineHeight;

        return $this;
    }

    /**
     * Set the letter spacing.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\SizeValue $letterSpacing
     *
     * @return $this
     */
    public function setLetterSpacing(SizeValue $letterSpacing)
    {
        $this->letterSpacing = $letterSpacing;

        return $this;
    }

    /**
     * Get the font family.
     *
     * @return string|-1
     *
     * @example 'Verdana'
     */
    public function getFontFamily()
    {
        return $this->fontFamily;
    }

    /**
     * Get the font size.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\SizeValue||-1
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * Get the font weight.
     *
     * @return string|-1
     */
    public function getFontWeight()
    {
        return $this->fontWeight;
    }

    /**
     * Get the font color.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\ColorValue||-1
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Get the text decoration.
     *
     * @return string|-1
     *
     * @example 'underline'
     * @example 'none'
     */
    public function getTextDecoration()
    {
        return $this->textDecoration;
    }

    /**
     * Get the text transform.
     *
     * @return string|-1
     */
    public function getTextTransform()
    {
        return $this->textTransform;
    }

    /**
     * Get the font style.
     *
     * @return string|-1
     */
    public function getFontStyle()
    {
        return $this->fontStyle;
    }

    /**
     * Get the letter spacing.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\SizeValue||-1
     */
    public function getLetterSpacing()
    {
        return $this->letterSpacing;
    }

    /**
     * Get the line height.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\SizeValue||-1
     */
    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Value\Value::toStyleString()
     */
    public function toStyleString()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Value\Value::toLessVariablesArray()
     */
    public function toLessVariablesArray()
    {
        $variables = [];
        $variable = $this->getVariable();
        if ($this->getFontFamily() !== -1) {
            $variables[$variable . '-type-font-family'] = $this->getFontFamily();
        }
        if ($this->getColor() !== -1) {
            $variables[$variable . '-type-color'] = $this->getColor()->toStyleString();
        }
        if ($this->getTextDecoration() !== -1) {
            $variables[$variable . '-type-text-decoration'] = $this->getTextDecoration();
        }
        if ($this->getTextTransform() !== -1) {
            $variables[$variable . '-type-text-transform'] = $this->getTextTransform();
        }
        if ($this->getFontStyle() !== -1) {
            $variables[$variable . '-type-font-style'] = $this->getFontStyle();
        }
        if ($this->getFontWeight() !== -1) {
            $variables[$variable . '-type-font-weight'] = $this->getFontWeight();
        }
        if ($this->getFontSize() !== -1 && $this->getFontSize()->hasSize()) {
            $variables[$variable . '-type-font-size'] = $this->getFontSize()->toStyleString();
        }
        if ($this->getLineHeight() !== -1 && $this->getLineHeight()->hasSize()) {
            $variables[$variable . '-type-line-height'] = $this->getLineHeight()->toStyleString();
        }
        if ($this->getLetterSpacing() !== -1 && $this->getLetterSpacing()->hasSize()) {
            $variables[$variable . '-type-letter-spacing'] = $this->getLetterSpacing()->toStyleString();
        }

        return $variables;
    }
}
