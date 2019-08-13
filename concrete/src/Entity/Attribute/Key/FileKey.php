<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FileAttributeKeys")
 * @since 8.0.0
 */
class FileKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'file';
    }

}
