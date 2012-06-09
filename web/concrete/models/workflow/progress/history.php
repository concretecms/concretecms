<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class WorkflowProgressHistory extends Object {  

	public function getWorkflowProgressHistoryTimestamp() {return $this->timestamp;}
	public function getWorkflowProgressHistoryID() {return $this->wphID;}
	public function getWorkflowProgressID() {return $this->wpID;}
	public function getWorkflowProgressHistoryInnerObject() {return $this->object;}
	
	public static function getByID($wphID) {
		$class = get_called_class();
		$db = Loader::db();
		$row = $db->GetRow('select * from WorkflowProgressHistory where wphID = ?', array($wphID));
		if (is_array($row) && ($row['wphID'])) {
			$obj = new $class();
			$obj->setPropertiesFromArray($row);
			$obj->object = @unserialize($row['object']);
			return $obj;
		}
	}
	
	public function getWorkflowProgressHistoryDescription() {
		if ($this->object instanceof WorkflowRequest) {
			$d = $this->object->getWorkflowRequestDescriptionObject();
			$ui = UserInfo::getByID($this->object->getRequesterUserID());
			return $d->getHTML() . ' ' . t('Originally requested by %s.', $ui->getUserName());
		}
		if ($this->object instanceof WorkflowHistoryEntry) {
			$d = $this->object->getWorkflowProgressHistoryDescription();
			return $d;
		}
	}
	
	public static function getList(WorkflowProgress $wp) {
		$db = Loader::db();
		$r = $db->Execute('select wphID from WorkflowProgressHistory where wpID = ? order by timestamp desc', array($wp->getWorkflowProgressID()));
		$class = get_called_class();		
		$list = array();
		while ($row = $r->FetchRow()) {
			$obj = call_user_func_array(array($class, 'getByID'), array($row['wphID']));
			if (is_object($obj)) {
				$list[] = $obj;
			}
		}
		return $list;
	}
}

class WorkflowHistoryEntry {

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