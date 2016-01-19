<?php
namespace Concrete\Core\Express;

class BaseEntity
{
    public function get($property)
    {
        $property = camelcase($property);
        $method = "get{$property}";

        return $this->$method();
    }

    public function getAttributeValueObject($attribute)
    {
        if (is_object($attribute)) {
            $attribute = $attribute->getAttributeKeyHandle();
        }
        return $this->get($attribute);
    }
}
