<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

class StyleValue implements \JsonSerializable
{

    /**
     * @var StyleInterface
     */
    protected $style;

    /**
     * @var ValueInterface
     */
    protected $value;

    /**
     * StyleValue constructor.
     * @param StyleInterface $style
     * @param ValueInterface $value
     */
    public function __construct(StyleInterface $style, ValueInterface $value)
    {
        $this->style = $style;
        $this->value = $value;
    }

    /**
     * @return StyleInterface
     */
    public function getStyle(): StyleInterface
    {
        return $this->style;
    }

    /**
     * @return ValueInterface
     */
    public function getValue(): ValueInterface
    {
        return $this->value;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['style' => $this->style, 'value' => $this->value];
    }


}
