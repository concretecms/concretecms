<?php
namespace Concrete\Core\Permission\Registry\Entry\Access\Entity;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

interface EntityInterface extends EntrySubjectInterface
{

    /**
     * @return Entity
     */
    function getAccessEntity();
}

