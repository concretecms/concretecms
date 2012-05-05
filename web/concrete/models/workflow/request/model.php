<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
abstract class WorkflowRequest extends Object {  
	
	public function __construct(PermissionKey $pk) {
		$u = new User();
		$this->uID = $u->getUserID();
		$this->pkID = $pk->getPermissionKeyID();
	}

	public function getWorkflowRequestID() { return $this->wrID;}
	
	public static function getByID($wrID) {
		$db = Loader::db();
		$wrObject = $db->getOne('select wrObject from WorkflowRequests where wrID = ?', array($wrID));
		if ($wrObject) {
			$wr = unserialize($wrObject);
			return $wr;
		}
	}
	
	public function save() {
		$db = Loader::db();
		if (!$this->wrID) {
			$wrObject = '';
			$db->Execute('insert into WorkflowRequestObjects (wrObject) values (?)', array($wrObject));
			$this->wrID = $db->Insert_ID();
		}
		$wrObject = serialize($this);
		$db->Execute('update WorkflowRequestObjects set wrObject = ? where wrID = ?', array($wrObject, $this->wrID));
	}
	
	/** 
	 * Triggers a workflow request, queries a permission key to see what workflows are attached to it
	 * and initiates them
	 * @return void
	 */
	public function trigger() {
		if (!$this->wrID) {
			$this->save();
		}
		
		$pk = PermissionKey::getByID($this->pkID);
		$workflows = $pk->getAssignedWorkflows();
		$wpObjects = array();
		foreach($workflows as $wf) {
			$this->addWorkflowProgress($wf);
		}
		return $wpObjects;
	}
	
	abstract function addWorkflowProgress(Workflow $wf);
}
