<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteTypeAttributeKeys")
 */
class SiteTypeKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'site_type';
    }


}
