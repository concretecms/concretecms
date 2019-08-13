<?php
namespace Concrete\Core\Permission\Registry\Entry\Access\Entity;

use Concrete\Core\Permission\Access\Entity\Entity as AccessEntity;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

/**
 * @since 8.0.0
 */
interface EntityInterface extends EntrySubjectInterface
{

    /**
     * @return AccessEntity
     */
    function getAccessEntity();
}

