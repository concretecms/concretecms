<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FileSearchColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'FileAttributeKey';
	public function getCurrent() {
		$u = new User();
		$fldc = $u->config('FILE_LIST_DEFAULT_COLUMNS');
		if ($fldc != '') {
			$fldc = @unserialize($fldc);
		}
		if (!($fldc instanceof DatabaseItemListColumnSet)) {
			$fldc = new FileSearchDefaultColumnSet();
		}
		return $fldc;
	}
}