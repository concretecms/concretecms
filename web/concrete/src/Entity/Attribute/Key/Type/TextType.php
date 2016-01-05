<?php

namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\TextValue;


/**
 * @Entity
 * @Table(name="TextAttributeKeyTypes")
 */
class TextType extends Type
{

    public function getAttributeValue()
    {
        return new TextValue();
    }

    public function createController()
    {
        $controller = new \Concrete\Attribute\Text\Controller($this->getAttributeType());
        return $controller;
    }

}
