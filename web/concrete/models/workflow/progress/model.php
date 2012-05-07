<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
abstract class WorkflowProgress extends Object {  

	protected $wpID;
	protected $wpDateAdded;
	
	/** 
	 * Gets the ID of the progress object
	 */
	public function getWorkflowProgressID() {return $this->wpID;}
	
	/** 
	 * Gets the date the WorkflowProgress object was added
	 * @return datetime
	 */
	public function getWorkflowProgressDateAdded() {return $this->wpDateAdded;}
	
	/** 
	 * Get the WorkflowRequest object for the current WorkflowProgress object
	 * @return WorkflowRequest
	 */
	public function getWorkflowRequestObject() {
		if ($this->wrID > 0) { 
			$cc = get_called_class();
			$class = substr($cc, 0, strpos($cc, 'WorkflowProgress')) . 'WorkflowRequest';			
			$wr = call_user_func_array(array($class, 'getByID'), array($this->wrID));
			$wr->setCurrentWorkflowProgressObject($this);
			return $wr;
		}
	}
	
	/** 
	 * Creates a WorkflowProgress object (which will be assigned to a Page, File, etc... in our system.
	 */
	public static function add(Workflow $wf, WorkflowRequest $wr) {
		$db = Loader::db();
		$wpDateAdded = Loader::helper('date')->getLocalDateTime();
		$db->Execute('insert into WorkflowProgress (wfID, wrID, wpDateAdded) values (?, ?, ?)', array(
			$wf->getWorkflowID(), $wr->getWorkflowRequestID(), $wpDateAdded
		));		
		return self::getByID($db->Insert_ID());
	}

	public function delete() {
		$db = Loader::db();
		$wr = $this->getWorkflowRequestObject();
		$db->Execute('delete from WorkflowProgress where wpID = ?', array($this->wpID));
		// now we clean up any WorkflowRequests that aren't in use any longer
		$cnt = $db->GetOne('select count(wpID) from WorkflowProgress where wrID = ?', array($this->wrID));
		if ($cnt == 0) {
			$wr->delete();
		}
	}
	public static function getByID($wpID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from WorkflowProgress where wpID  = ?', array($wpID));
		if (!is_array($r) && (!$r['wpID'])) { 
			return false;
		}
		
		$class = get_called_class();
		$wp = new $class;
		$wp->setPropertiesFromArray($r);
		return $wp;
	}

	public static function getRequestedTask() {
		$task = '';
		foreach($_POST as $key => $value) {
			if (strpos($key, 'action_') > -1) {
				return substr($key, 7);	
			}
		}
	}
	
	/** 
	 * Attempts to run a workflow task on the bound WorkflowRequest object first, then if that doesn't exist, attempts to run 
	 * it on the current WorkflowProgress object
	 * @return WorkflowProgressResponse
	 */
	public function runTask($task) {
		$task = 'action_' . $task;
		$wr = $this->getWorkflowRequestObject();
		if (method_exists($wr, $task)) {
			return call_user_func_array(array($wr, $task), array($this));
		}
		if (method_exists($this, $task)) {
			return call_user_func(array($this, $task));
		}		
	}
	
	abstract function getWorkflowProgressFormAction();

	
	
}
