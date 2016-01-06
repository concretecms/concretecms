<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue;
use PortlandLabs\Concrete5\MigrationTool\Batch\Formatter\Attribute\SocialLinksFormatter;
use PortlandLabs\Concrete5\MigrationTool\Publisher\Attribute\SocialLinksPublisher;

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
        $controller = new \Concrete\Attribute\SocialLinks\Controller($this->getAttributeType());
        return $controller;
    }
}
