<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteAttributeKeys")
 * @since 8.0.0
 */
class SiteKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'site';
    }


}
