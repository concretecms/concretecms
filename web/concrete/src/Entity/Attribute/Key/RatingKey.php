<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\RatingValue;


/**
 * @Entity
 * @Table(name="RatingAttributeKeys")
 */
class RatingKey extends Key
{

    public function getTypeHandle()
    {
        return 'rating';
    }

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
