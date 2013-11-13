<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageSearchAvailableColumnSet extends PageSearchDefaultColumnSet {
	protected $attributeClass = 'CollectionAttributeKey';
	public function __construct() {
		parent::__construct();
	}
}