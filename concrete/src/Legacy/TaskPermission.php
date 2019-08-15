<?php

namespace Concrete\Core\Legacy;

/**
 * @deprecated Use \Concrete\Core\Permission\Checker
 */
final class TaskPermission extends \Concrete\Core\Permission\Checker
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
        $pk = \Concrete\Core\Permission\Key\Key::getByHandle($handle);

        return $pk;
    }
}
