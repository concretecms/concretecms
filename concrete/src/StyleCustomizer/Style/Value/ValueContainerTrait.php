<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

use Concrete\Core\StyleCustomizer\Style\StyleValue;

trait ValueContainerTrait
{

    protected $styleValues = [];

    public function hasSubStyleValues(): bool
    {
        return count($this->styleValues) > 0;
    }

    public function addSubStyleValue(StyleValue $styleValue)
    {
        $this->styleValues[] = $styleValue;
    }

    /**
     * @return array
     */
    public function getSubStyleValues(): array
    {
        return $this->styleValues;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['values' => $this->styleValues];
    }

}
