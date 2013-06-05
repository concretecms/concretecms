<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * legacy
 * @private
 */
final class TaskPermission extends Permissions {
	
	public function getByHandle($handle) {
		$pk = PermissionKey::getByHandle($handle);
		return $pk;
	}
	
}