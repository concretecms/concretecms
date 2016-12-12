<?php
namespace Concrete\Core\Legacy;

/**
 * @deprecated
 */
final class TaskPermission extends \Permissions
{
    public function getByHandle($handle)
    {
        $pk = \PermissionKey::getByHandle($handle);

        return $pk;
    }
}
