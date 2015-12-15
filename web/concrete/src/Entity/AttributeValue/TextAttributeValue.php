<?php

namespace Concrete\Core\Entity\AttributeValue;


/**
 * @Entity
 * @Table(name="TextAttributeValues")
 */
class TextAttributeValue extends AttributeValue
{

    /**
     * @Column(type="string", nullable=true)
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

}
