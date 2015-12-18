<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\ImageFileValue;


/**
 * @Entity
 * @Table(name="ImageFileAttributeKeys")
 */
class ImageFileKey extends Key
{

    public function getTypeHandle()
    {
        return 'image_file';
    }

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
