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
     * SizeValue constructor.
     * @param mixed $size
     * @param string $unit
     */
    public function __construct($size, string $unit)
    {
        $this->size = $size;
        $this->unit = $unit;
    }

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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'size' => $this->getSize(),
            'unit' => $this->getUnit(),
        ];
    }
}
