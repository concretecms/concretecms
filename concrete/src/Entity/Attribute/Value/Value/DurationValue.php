<?php

namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atDuration")
 */
class DurationValue extends AbstractValue
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $value = 0;

    /**
     * @return mixed
     */
    public function getValue()
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
        return (string)$this->getValue();
    }
}
