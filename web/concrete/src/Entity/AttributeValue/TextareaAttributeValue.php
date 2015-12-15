<?php

namespace Concrete\Core\Entity\AttributeValue;


/**
 * @Entity
 * @Table(name="TextareaAttributeValues")
 */
class TextareaAttributeValue extends AttributeValue
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


}
