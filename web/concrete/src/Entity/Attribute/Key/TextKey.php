<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\TextValue;


/**
 * @Entity
 * @Table(name="TextAttributeKeys")
 */
class TextKey extends Key
{

    public function getTypeHandle()
    {
        return 'text';
    }

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
