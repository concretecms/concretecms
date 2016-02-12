<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\RatingValue;

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

    public function getAttributeTypeHandle()
    {
        return 'rating';
    }

    public function createController()
    {
        $controller = \Core::make('\Concrete\Attribute\Rating\Controller');
        $controller->setAttributeType($this->getAttributeType());

        return $controller;
    }
}
