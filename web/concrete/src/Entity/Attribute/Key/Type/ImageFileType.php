<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue;

/**
 * @Entity
 * @Table(name="ImageFileAttributeKeyTypes")
 */
class ImageFileType extends Type
{
    public function getAttributeValue()
    {
        return new ImageFileValue();
    }

    public function createController()
    {
        $controller = new \Concrete\Attribute\ImageFile\Controller($this->getAttributeType());

        return $controller;
    }
}
