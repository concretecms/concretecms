<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;

class NumberVariable implements VariableInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $unit;

    /**
     * @var string|float
     */
    protected $number;

    /**
     * @return float|string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param float|string $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * @return string|null
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * @param string|null $unit
     */
    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function __construct(string $name, $number, $unit = null)
    {
        $this->unit = $unit;
        $this->number = $number;
        $this->name = $name;
    }

    public function getValue()
    {
        return $this->getNumber() . $this->getUnit();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'type' => 'number',
            'name' => $this->getName(),
            'number' => $this->getNumber(),
            'unit' => $this->getUnit()
        ];
    }


}
