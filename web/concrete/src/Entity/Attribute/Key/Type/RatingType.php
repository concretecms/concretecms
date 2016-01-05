<?php

namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\RatingValue;


/**
 * @Entity
 * @Table(name="RatingAttributeKeyTypes")
 */
class RatingType extends Type
{

    public function getAttributeValue()
    {
        return new RatingValue();
    }

    public function createController()
    {
        $controller = new \Concrete\Attribute\Rating\Controller($this->getAttributeType());
        return $controller;
    }

}
