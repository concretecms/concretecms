<?php
namespace Concrete\Core\Search;

interface PermissionableListItemInterface
{
    public function checkPermissions($mixed);
    public function setPermissionsChecker(\Closure $callback);
    public function ignorePermissions();
    /**
     * @since 8.2.1
     */
    public function getPermissionsChecker();
    /**
     * @since 8.2.1
     */
    public function enablePermissions();

}
