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
    protected $value = null;

    /**
     * @return string|null
     */
    public function getValue()
    {
        if ($this->value === null) {
            return null;
        } else {
            return \Core::make('helper/number')->trim($this->value);
        }
    }

    /**
     * @param string|float|int|null $value
     */
    public function setValue($value)
    {
        if ($value === null || $value === '') {
            $this->value = null;
        } else {
            $this->value = (string) $value;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see Value::__toString()
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
