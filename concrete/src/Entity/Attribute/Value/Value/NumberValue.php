<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="NumberAttributeValues")
 */
class NumberValue extends Value
{
    /**
     * @ORM\Column(type="decimal", precision=14, scale=4, nullable=true)
     */
    protected $value = '';

    /**
     * @return mixed
     */
    public function getValue()
    {
        $number = \Core::make('helper/number');
        return (string) $number->flexround($this->getUnroundedValue());
    }

    public function getUnroundedValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }
}
