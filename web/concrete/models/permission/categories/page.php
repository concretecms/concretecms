<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionKey extends PermissionKey {
	
	public static function getByID($pkID) {
		$pk = new self;
		$pk->load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			return $pk;
		}
	}
	

}