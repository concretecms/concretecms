<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageSearchColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'CollectionAttributeKey';
	public function getCurrent() {
		$u = new User();
		$fldc = $u->config('PAGE_LIST_DEFAULT_COLUMNS');
		if ($fldc != '') {
			$fldc = @unserialize($fldc);
		}
		if (!($fldc instanceof DatabaseItemListColumnSet)) {
			$fldc = new PageSearchDefaultColumnSet();
		}
		return $fldc;
	}
}