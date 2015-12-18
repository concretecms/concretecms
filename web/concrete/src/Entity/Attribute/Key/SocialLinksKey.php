<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\SocialLinksValue;
use PortlandLabs\Concrete5\MigrationTool\Batch\Formatter\Attribute\SocialLinksFormatter;
use PortlandLabs\Concrete5\MigrationTool\Publisher\Attribute\SocialLinksPublisher;

/**
 * @Entity
 * @Table(name="SocialLinkAttributeKeys")
 */
class SocialLinksKey extends Key
{
    public function getTypeHandle()
    {
        return 'social_links';
    }

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
