<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Foundation\Environment;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSetAttributeKeyControls")
 */
class AttributeKeyControl extends Control
{
    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\AttributeKey\AttributeKey"}))
     */
    protected $attribute_key;

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    /**
     * @param mixed $attribute_key
     */
    public function setAttributeKey($attribute_key)
    {
        $this->attribute_key = $attribute_key;
    }

    public function getFormRenderer()
    {

    }


}