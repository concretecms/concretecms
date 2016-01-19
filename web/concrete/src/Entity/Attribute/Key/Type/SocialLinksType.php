<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue;

/**
 * @Entity
 * @Table(name="SocialLinkAttributeKeyTypes")
 */
class SocialLinksType extends Type
{
    public function getAttributeValue()
    {
        return new SocialLinksValue();
    }

    public function createController()
    {
        $controller = \Core::make('\Concrete\Attribute\SocialLinks\Controller');
        $controller->setAttributeType($this->getAttributeType());

        return $controller;
    }
}
