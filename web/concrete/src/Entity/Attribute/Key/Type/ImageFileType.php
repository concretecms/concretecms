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

    public function getAttributeTypeHandle()
    {
        return 'image_file';
    }

    public function createController()
    {
        $controller = \Core::make('\Concrete\Attribute\ImageFile\Controller');
        $controller->setAttributeType($this->getAttributeType());

        return $controller;
    }
}
