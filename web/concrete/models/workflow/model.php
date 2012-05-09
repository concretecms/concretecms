<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Workflow extends Object {  
	
	protected $wfID = 0;

	public function getWorkflowID() {return $this->wfID;}
	public function getWorkflowName() {return $this->wfName;}
	public function getWorkflowTypeObject() {
		return WorkflowType::getByID($this->wftID);
	}
	
	public static function getList() {
		$workflows = array();
		$db = Loader::db();
		$r = $db->Execute("select wfID from Workflows order by wfName asc");
		while ($row = $r->FetchRow()) {
			$wf = Workflow::getByID($row['wfID']);
			if (is_object($wf)) {
				$workflows[] = $wf;
			}	
		}
		return $workflows;
	}
	
	public static function add(WorkflowType $wt, $name) {
		$db = Loader::db();
		$db->Execute('insert into Workflows (wftID, wfName) values (?, ?)', array($wt->getWorkflowTypeID(), $name));
		$wfID = $db->Insert_ID();
		return self::getByID($wfID);
	}
	
	public static function getByID($wfID) {
		$class = get_called_class();
		$obj = new $class();
		$db = Loader::db();
		$r = $db->GetRow('select * from Workflows where wfID = ?', array($wfID));
		if (is_array($r) && $r['wfID'] == $wfID) {
			$obj->setPropertiesFromArray($r);
			return $obj;
		}
	}

	public function getWorkflowToolsURL($task) {
		$type = $this->getWorkflowTypeObject();
		$uh = Loader::helper('concrete/urls');
		$url = $uh->getToolsURL('workflow/types/' . $type->getWorkflowTypeHandle(), $type->getPackageHandle());
		$url .= '?wfID=' . $this->getWorkflowID() . '&task=' . $task . '&' . Loader::helper('validation/token')->getParameter($task);
		return $url;
	}
	
	public function __call($nm, $args) {
		$type = $this->getWorkflowTypeObject();
		$targs = array_merge(array($this), $args);
		return call_user_func_array(array($type, $nm), $targs);
	}

	
}
