<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Permission\Key\Key;

/**
 * @since 8.0.0
 */
interface AssignableObjectInterface
{
    function setPermissionsToOverride();
    function setChildPermissionsToOverride();
    function assignPermissions($userOrGroup, $permissions, $accessType = Key::ACCESS_TYPE_INCLUDE, $cascadeToChildren = true);

}