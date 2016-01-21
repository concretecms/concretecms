<?php
namespace Concrete\Core\Entity\Attribute\Key;

/**
 * @Entity
 * @Table(name="FileAttributeKeys")
 */
class FileKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'file';
    }

}
