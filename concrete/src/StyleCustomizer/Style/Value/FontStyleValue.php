<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class FontStyleValue extends Value
{
    /**
     * @var string
     */
    protected $fontStyle;

    /**
     * FontStyleValue constructor.
     * @param string $fontStyle
     */
    public function __construct(string $fontStyle)
    {
        $this->fontStyle = $fontStyle;
    }

    /**
     * @return string
     */
    public function getFontStyle(): string
    {
        return $this->fontStyle;
    }

    /**
     * @param string $fontStyle
     */
    public function setFontStyle(string $fontStyle): void
    {
        $this->fontStyle = $fontStyle;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'fontStyle' => $this->getFontStyle(),
        ];
    }




}
