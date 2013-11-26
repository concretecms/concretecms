<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FileSearchAvailableColumnSet extends FileSearchDefaultColumnSet {
	protected $attributeClass = 'FileAttributeKey';
	public function __construct() {
		parent::__construct();
		$this->addColumn(new DatabaseItemListColumn('fvAuthorName', t('Author'), 'getAuthorName'));
	}
}