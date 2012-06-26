<?
defined('C5_EXECUTE') or die("Access Denied.");

class FileSetPermissionKey extends PermissionKey {
	protected $permissionObjectToCheck;
}

class FilePermissions {

	public static function getGlobal() {
		$fs = FileSet::getGlobal();
		$fsp = new Permissions($fs);
		return $fsp;
	}
}