<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;

class BasicValue extends Value
{
    protected $value;

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toStyleString()
    {
        return false;
    }

    public function toLessVariablesArray()
    {
        return array($this->getVariable() => '"' . $this->getValue() . '"'); // we have to quote these, i don't know why
    }
}
