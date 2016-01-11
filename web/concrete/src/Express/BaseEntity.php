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

    public function getAttribute($handle)
    {
        return $this->get($handle);
    }
}
