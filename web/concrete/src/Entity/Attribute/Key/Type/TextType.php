<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\TextValue;

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

    public function getAttributeTypeHandle()
    {
        return 'text';
    }

    public function createController()
    {

        $controller = \Core::make('\Concrete\Attribute\Text\Controller');
        $controller->setAttributeType($this->getAttributeType());

        return $controller;
    }
}
