<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Permission\Key\Key;

interface AssignableObjectInterface
{
    function assignPermissions($userOrGroup, $permissions, $accessType = Key::ACCESS_TYPE_INCLUDE);
}