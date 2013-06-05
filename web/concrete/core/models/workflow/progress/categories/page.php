<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Model_PageWorkflowProgress extends WorkflowProgress {  
	
	protected $cID;

	public static function add(Workflow $wf, PageWorkflowRequest $wr) {
		$wp = parent::add('page', $wf, $wr);
		$db = Loader::db();
		$db->Replace('PageWorkflowProgress', array('cID' => $wr->getRequestedPageID(), 'wpID' => $wp->getWorkflowProgressID()), array('cID', 'wpID'), true);
		$wp->cID = $wr->getRequestedPageID();
		return $wp;
	}
	
	public function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select cID from PageWorkflowProgress where wpID = ?', array($this->wpID));
		$this->setPropertiesFromArray($row);		
	}
	
	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from PageWorkflowProgress where wpID = ?', array($this->wpID));
	}
	
	public static function getList(Page $c, $filters = array('wpIsCompleted' => 0), $sortBy = 'wpDateAdded asc') {
		$db = Loader::db();
		$filter = '';
		foreach($filters as $key => $value) {
			$filter .= ' and ' . $key . ' = ' . $value . ' ';
		}
		$filter .= ' order by ' . $sortBy;
		$r = $db->Execute('select wp.wpID from PageWorkflowProgress pwp inner join WorkflowProgress wp on pwp.wpID = wp.wpID where cID = ? ' . $filter, array($c->getCollectionID()));
		$list = array();
		while ($row = $r->FetchRow()) {
			$wp = PageWorkflowProgress::getByID($row['wpID']);
			if (is_object($wp)) {
				$list[] = $wp;
			}
		}
		return $list;
	}

	public function getWorkflowProgressFormAction() {
		return REL_DIR_FILES_TOOLS_REQUIRED . '/' . DIRNAME_WORKFLOW . '/categories/page?task=save_workflow_progress&cID=' . $this->cID . '&wpID=' . $this->getWorkflowProgressID() . '&' . Loader::helper('validation/token')->getParameter('save_workflow_progress');
	}

	public function getPendingWorkflowProgressList() {
		$list = new PageWorkflowProgressList();
		$list->filter('wpApproved', 0);
		$list->sortBy('wpDateLastAction', 'desc');
		return $list;
	}
	
	
}

class Concrete5_Model_PageWorkflowProgressList extends PageList {
	
	protected $autoSortColumns = array('wpDateLastAction', 'cvName', 'wpCurrentStatus');
	
	public function __construct() {
		$this->includeInactivePages();
		$this->includeSystemPages();
		$this->displayUnapprovedPages();
		$this->ignoreAliases();
		parent::setBaseQuery(', pwp.wpID, wp.wpCurrentStatus');
		$this->addToQuery('inner join PageWorkflowProgress pwp on p1.cID = pwp.cID inner join WorkflowProgress wp on wp.wpID = pwp.wpID');
		$this->filter('wpIsCompleted', 0);
	}

	public function get($itemsToGet = 0, $offset = 0) {
		$_pages = DatabaseItemList::get($itemsToGet, $offset);
		$pages = array();
		foreach($_pages as $row) {
			$c = Page::getByID($row['cID']);
			$cp = new Permissions($c);
			if ($cp->canViewPageVersions()) { 
				$c->loadVersionObject('RECENT');
			} else {
				$c->loadVersionObject('ACTIVE');
			}
			$wp = PageWorkflowProgress::getByID($row['wpID']);
			$pages[] = new PageWorkflowProgressPage($c, $wp);
		}
		return $pages;
	}
}

class Concrete5_Model_PageWorkflowProgressHistory extends WorkflowProgressHistory {

}

class Concrete5_Model_PageWorkflowProgressPage {

	public function __construct(Page $p, WorkflowProgress $wp) {
		$this->page = $p;
		$this->wp = $wp;
	}
	
	public function getPageObject() {return $this->page;}
	public function getWorkflowProgressObject() {return $this->wp;}
	
}