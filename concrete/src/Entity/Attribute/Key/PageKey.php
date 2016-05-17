<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CollectionAttributeKeys")
 */
class PageKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'collection';
    }


}
