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
     * @return float|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float|null $value
     */
    public function setValue($value)
    {
        if ($value === '' || $value === null) {
            $this->value = null;
        } else {
            $this->value = (float) $value;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see Value::__toString()
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
