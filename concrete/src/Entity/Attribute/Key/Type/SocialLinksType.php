<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SocialLinkAttributeKeyTypes")
 */
class SocialLinksType extends Type
{
    public function getAttributeValue()
    {
        return new SocialLinksValue();
    }

}
