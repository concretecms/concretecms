<?php
namespace Concrete\Core\Entity\Attribute\Key;

/**
 * @Entity
 * @Table(name="CollectionAttributeKeys")
 */
class PageKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'collection';
    }


}
