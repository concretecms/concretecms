<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_UserSearchColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'UserAttributeKey';
	public function getColumns() {
		$columns = array();
		$pk = PermissionKey::getByHandle('view_user_attributes');
		foreach($this->columns as $col) {
			if ($col instanceof DatabaseItemListAttributeKeyColumn) {
				$uk = $col->getAttributeKey();
				if ($pk->validate($uk)) {
					$columns[] = $col;
				}
			} else {
				$columns[] = $col;
			}
		}
		return $columns;
	}
	
	public function getCurrent() {
		$u = new User();
		$fldc = $u->config('USER_LIST_DEFAULT_COLUMNS');
		if ($fldc != '') {
			$fldc = @unserialize($fldc);
		}
		if (!($fldc instanceof DatabaseItemListColumnSet)) {
			$fldc = new UserSearchDefaultColumnSet();
		}
		return $fldc;
	}
}