<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class BasicValue extends Value
{
    /**
     * The CSS value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Set the CSS value.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the CSS value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
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
        return [$this->getVariable() => '"' . $this->getValue() . '"']; // we have to quote these, i don't know why
    }
}
