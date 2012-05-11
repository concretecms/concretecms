<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
abstract class Workflow extends Object {  
	
	protected $wfID = 0;
	protected $allowedTasks = array('cancel', 'approve');
	
	public function getAllowedTasks() {return $this->allowedTasks;}
	
	public function getWorkflowID() {return $this->wfID;}
	public function getWorkflowName() {return $this->wfName;}
	public function getWorkflowTypeObject() {
		return WorkflowType::getByID($this->wftID);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from Workflows where wfID = ?', array($this->wfID));
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
	
	protected function load($wfID) {
		$db = Loader::db();
		$r = $db->GetRow('select Workflows.* from Workflows where Workflows.wfID = ?', array($wfID));
		$this->setPropertiesFromArray($r);
	}
	
	final static function getByID($wfID) {
		$db = Loader::db();
		$r = $db->GetRow('select WorkflowTypes.wftHandle, WorkflowTypes.pkgID from Workflows inner join WorkflowTypes on Workflows.wftID = WorkflowTypes.wftID where Workflows.wfID = ?', array($wfID));
		if ($r['wftHandle']) { 
			$file = Loader::helper('concrete/path')->getPath(DIRNAME_MODELS . '/' . DIRNAME_WORKFLOW . '/' . DIRNAME_SYSTEM_TYPES . '/' . $r['wftHandle'] . '.php', $r['pkgID']);
			require_once($file);
			$class = Loader::helper('text')->camelcase($r['wftHandle']) . 'Workflow';
			$obj = new $class();
			$obj->load($wfID);
			if ($obj->getWorkflowID() > 0) { 
				return $obj;
			}
		}
	}

	public function getWorkflowToolsURL($task) {
		$type = $this->getWorkflowTypeObject();
		$uh = Loader::helper('concrete/urls');
		$url = $uh->getToolsURL('workflow/types/' . $type->getWorkflowTypeHandle(), $type->getPackageHandle());
		$url .= '?wfID=' . $this->getWorkflowID() . '&task=' . $task . '&' . Loader::helper('validation/token')->getParameter($task);
		return $url;
	}
	
	abstract public function start(WorkflowProgress $wp);
	abstract public function getWorkflowProgressActions(WorkflowProgress $wp);
	
}
