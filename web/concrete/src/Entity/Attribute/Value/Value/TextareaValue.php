<?php

namespace Concrete\Core\Entity\Attribute\Value\Value;

/**
 * @Entity
 * @Table(name="TextareaAttributeValues")
 */
class TextareaValue extends Value
{

    /**
     * @Column(type="text", nullable=true)
     */
    protected $value = '';

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
        return $this->getValue();
    }


}
