<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class SizeValue extends Value
{
    /**
     * The numeric amount of the size.
     *
     * @var mixed
     */
    protected $size;

    /**
     * The unit of the size.
     *
     * @var string
     */
    protected $unit = 'px';

    /**
     * Set the numeric amount of the size.
     *
     * @param mixed $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set the unit of the size.
     *
     * @param string $unit
     *
     * @return $this
     */
    public function setUnit($unit)
    {
        $this->unit = (string) $unit;

        return $this;
    }

    /**
     * Get the numeric amount of the size.
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get the unit of the size.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @deprecated Use getUnit()
     *
     * @return string
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Value\SizeValue::getUnit()
     */
    public function getUnits()
    {
        return $this->getUnit();
    }

    /**
     * Does this value has the numeric amount of the size?
     *
     * @return bool
     */
    public function hasSize()
    {
        return (string) $this->getSize() !== '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Value\Value::toStyleString()
     */
    public function toStyleString()
    {
        return $this->hasSize() ? $this->getSize() . $this->getUnit() : '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Value\Value::toLessVariablesArray()
     */
    public function toLessVariablesArray()
    {
        return [$this->getVariable() . '-size' => $this->toStyleString()];
    }
}
