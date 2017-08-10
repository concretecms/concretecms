<?php
namespace Concrete\Core\Search;

interface PermissionableListItemInterface
{
    public function checkPermissions($mixed);
    public function setPermissionsChecker(\Closure $callback);
    public function ignorePermissions();
    public function getPermissionsChecker();
    public function enablePermissions();

}
