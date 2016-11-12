<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

interface ObjectInterface extends EntrySubjectInterface
{

    /**
     * @return AssignableObjectInterface
     */
    function getPermissionObject();

}
