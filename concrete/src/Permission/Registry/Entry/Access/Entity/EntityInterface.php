<?php
namespace Concrete\Core\Permission\Registry\Entry\Access\Entity;

use Concrete\Core\Permission\Access\Entity\Entity as AccessEntity;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

interface EntityInterface extends EntrySubjectInterface
{

    /**
     * @return \Concrete\Core\Permission\Access\Entity\Entity|\Concrete\Core\User\Group\Group|false|null the access entity (or a group, that AssignableObjectTrait::assignPermissions() converts to an access entity); NULL or false if it can't be resolved
     */
    function getAccessEntity();
}

