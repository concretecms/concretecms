<?php

namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\NumberValue;


/**
 * @Entity
 * @Table(name="NumberAttributeKeyTypes")
 */
class NumberType extends Type
{

    public function getAttributeValue()
    {
        return new NumberValue();
    }

    public function createController()
    {
        $controller = new \Concrete\Attribute\Number\Controller($this->getAttributeType());
        return $controller;
    }

}
