<?php

namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(name="BooleanAttributeValues")
 */
class BooleanValue extends Value
{

    /**
     * @Column(type="boolean")
     */
    protected $value = false;

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



}
