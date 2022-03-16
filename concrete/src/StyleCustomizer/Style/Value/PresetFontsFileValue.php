<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class PresetFontsFileValue extends Value
{

    protected $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return ['value' => $this->value];
    }


}
