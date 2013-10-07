<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * @access private
 */
 
/** 
 * @access private
 */

class Concrete5_Model_GroupSearch extends DatabaseItemList {
	
	
	protected $itemsPerPage = 10;
	protected $minimumGroupID = REGISTERED_GROUP_ID;
	
	public function includeAllGroups() {
		$this->minimumGroupID = -1;
	}
	
	public function filterByKeywords($kw) {
		$db = Loader::db();
		$this->filter(false, "(Groups.gName like " . $db->qstr('%' . $kw . '%') . " or Groups.gDescription like " . $db->qstr('%' . $kw . '%') . ")");
	}
	
	public function filterByAllowedPermission($pk) {
		$assignment = $pk->getMyAssignment();
		$r = $assignment->getGroupsAllowedPermission();
		$gIDs = array('-1');
		if ($r == 'C') {
			$gIDs = array_merge($assignment->getGroupsAllowedArray(), $gIDs);
			$this->filter('gID', $gIDs, 'in');
		}
	}
	
	public function updateItemsPerPage( $num ) {
		$this->itemsPerPage = $num;
	}
	
	function __construct() {
		$this->setQuery("select Groups.gID, Groups.gName, Groups.gDescription from Groups");
		$this->sortBy('gName', 'asc');
	}
	
	public function getPage() {
		$this->filter('gID', $this->minimumGroupID, '>');
		return parent::getPage();
	}
}