<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class FontFamilyValue extends Value
{
    /**
     * @var string
     */
    protected $fontFamily;

    /**
     * FontFamilyValue constructor.
     * @param string $fontFamily
     */
    public function __construct(string $fontFamily)
    {
        $this->fontFamily = $fontFamily;
    }

    /**
     * @return string
     */
    public function getFontFamily(): string
    {
        return $this->fontFamily;
    }

    /**
     * @param string $fontFamily
     */
    public function setFontFamily(string $fontFamily): void
    {
        $this->fontFamily = $fontFamily;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'fontFamily' => $this->getFontFamily(),
        ];
    }




}
