<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteAttributeKeys")
 */
class SiteKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'site';
    }


}
