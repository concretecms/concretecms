<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class FontWeightValue extends Value
{
    /**
     * @var string
     */
    protected $fontWeight;

    /**
     * FontWeightValue constructor.
     * @param string $fontWeight
     */
    public function __construct(string $fontWeight)
    {
        $this->fontWeight = $fontWeight;
    }

    /**
     * @return string
     */
    public function getFontWeight(): string
    {
        return $this->fontWeight;
    }

    /**
     * @param string $fontWeight
     */
    public function setFontWeight(string $fontWeight): void
    {
        $this->fontWeight = $fontWeight;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'fontWeight' => $this->getFontWeight(),
        ];
    }




}
