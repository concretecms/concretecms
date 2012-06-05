<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionSet {

	protected $permissions;

	public function addPermissionKey(PermissionKey $pk) {
		$this->permissions[] = $pk;
	}	

}