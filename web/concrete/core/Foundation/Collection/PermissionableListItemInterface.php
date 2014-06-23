<?
namespace Concrete\Core\Foundation\Collection;
interface PermissionableListItemInterface
{
    public function checkPermissions($mixed);
    public function setPermissionsChecker(\Closure $callback);
    public function ignorePermissions();

}