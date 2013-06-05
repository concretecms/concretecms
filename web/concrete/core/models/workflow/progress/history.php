<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Model_WorkflowProgressHistory extends Object {  

	public function getWorkflowProgressHistoryTimestamp() {return $this->timestamp;}
	public function getWorkflowProgressHistoryID() {return $this->wphID;}
	public function getWorkflowProgressID() {return $this->wpID;}
	public function getWorkflowProgressHistoryInnerObject() {return $this->object;}
	
	public function getWorkflowProgressHistoryDescription() {
		if ($this->object instanceof WorkflowRequest) {
			$d = $this->object->getWorkflowRequestDescriptionObject();
			$ui = UserInfo::getByID($this->object->getRequesterUserID());
			return $d->getDescription() . ' ' . t('Originally requested by %s.', $ui->getUserName());
		}
		if ($this->object instanceof WorkflowHistoryEntry) {
			$d = $this->object->getWorkflowProgressHistoryDescription();
			return $d;
		}
	}
	
	public static function getList(WorkflowProgress $wp) {
		$db = Loader::db();
		$r = $db->Execute('select wphID from WorkflowProgressHistory where wpID = ? order by timestamp desc', array($wp->getWorkflowProgressID()));
		$list = array();
		while ($row = $r->FetchRow()) {
			$obj = $wp->getWorkflowProgressHistoryObjectByID($row['wphID']);
			if (is_object($obj)) {
				$list[] = $obj;
			}
		}
		return $list;
	}
}

abstract class Concrete5_Model_WorkflowHistoryEntry {
	
	abstract public function getWorkflowProgressHistoryDescription();
	
	public function setAction($action) {
		$this->action = $action;
	}
	
	public function getAction() {
		return $this->action;
	}
	
	public function setRequesterUserID($uID) {
		$this->uID = $uID;
	}
	
	public function getRequesterUserID() {
		return $this->uID;
	}
	
}