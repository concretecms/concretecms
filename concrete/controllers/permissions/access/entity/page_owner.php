<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Permission\Access\Entity\PageOwnerEntity;

class PageOwner extends AccessEntity
{

    public function deliverEntity()
    {
        return PageOwnerEntity::getOrCreate();
    }
}
