<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Permission\Key\Key;

interface AssignableObjectInterface
{
    function setPermissionsToOverride();
    function setChildPermissionsToOverride();
    function assignPermissions($userOrGroup, $permissions, $accessType = Key::ACCESS_TYPE_INCLUDE, $cascadeToChildren = true);

}