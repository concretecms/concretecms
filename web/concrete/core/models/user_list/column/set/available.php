<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_UserSearchAvailableColumnSet extends UserSearchDefaultColumnSet {
	protected $attributeClass = 'UserAttributeKey';
	public function __construct() {
		parent::__construct();
	}
}
