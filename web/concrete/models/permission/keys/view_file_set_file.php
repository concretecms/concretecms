<?
defined('C5_EXECUTE') or die("Access Denied.");

class ViewFileSetFileFileSetPermissionKey extends FileSetPermissionKey  {

	public function getSupportedAccessTypes() {
		$types = array(
			self::ACCESS_TYPE_INCLUDE => t('Included'),
			self::ACCESS_TYPE_EXCLUDE => t('Excluded')
		);
		return $types;
	}


}

class ViewFileSetFileFileSetPermissionAssignment extends FileSetPermissionAssignment {}