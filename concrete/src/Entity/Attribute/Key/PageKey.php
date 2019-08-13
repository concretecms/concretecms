<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CollectionAttributeKeys")
 * @since 8.0.0
 */
class PageKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'collection';
    }


}
