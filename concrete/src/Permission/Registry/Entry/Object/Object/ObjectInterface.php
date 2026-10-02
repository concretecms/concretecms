<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\Registry\Entry\EntrySubjectInterface;

interface ObjectInterface extends EntrySubjectInterface
{

    /**
     * @return \Concrete\Core\Permission\AssignableObjectInterface|null NULL if the object can't be resolved
     */
    function getPermissionObject();

}
