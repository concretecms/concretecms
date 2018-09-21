<?php

namespace Concrete\Core\Legacy;

/**
 * @deprecated Use \Concrete\Core\Permission\Checker
 */
final class TaskPermission extends \Permissions
{
    /**
     * @deprecated Use \Concrete\Core\Permission\Key\Key::getByHandle()
     *
     * @param string $handle
     *
     * @return \Concrete\Core\Permission\Key\Key|null
     */
    public static function getByHandle($handle)
    {
        $pk = \PermissionKey::getByHandle($handle);

        return $pk;
    }
}
