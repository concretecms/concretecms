<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\NumberValue;


/**
 * @Entity
 * @Table(name="NumberAttributeKeys")
 */
class NumberKey extends Key
{

    public function getTypeHandle()
    {
        return 'number';
    }

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
