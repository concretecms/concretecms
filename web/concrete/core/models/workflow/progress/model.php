<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
abstract class Concrete5_Model_WorkflowProgress extends Object {  

	protected $wpID;
	protected $wpDateAdded;
	protected $wfID;
	protected $response;
	protected $wpDateLastAction;
	
	
	/** 
	 * Gets the Workflow object attached to this WorkflowProgress object
	 * @return Workflow
	 */
	public function getWorkflowObject() {
		if ($this->wfID > 0) {
			$wf = Workflow::getByID($this->wfID);
		} else {
			$wf = new EmptyWorkflow();
		}
		return $wf;
	}
	
	/** 
	 * Gets an optional WorkflowResponse object. This is set in some cases
	 */
	public function getWorkflowProgressResponseObject() {
		return $this->response;
	}
	
	public function setWorkflowProgressResponseObject($obj) {
		$this->response = $obj;
	}
	
	/** 
	 * Gets the date of the last action
	 */
	public function getWorkflowProgressDateLastAction() {
		return $this->wpDateLastAction;
	}
	
	/** 
	 * Gets the ID of the progress object
	 */
	public function getWorkflowProgressID() {return $this->wpID;}
	
	/** 
	 * Get the category ID
	 */
	public function getWorkflowProgressCategoryID() {return $this->wpCategoryID;}
	
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
			$cat = WorkflowProgressCategory::getByID($this->wpCategoryID);
			$handle = $cat->getWorkflowProgressCategoryHandle();
			$class = Loader::helper("text")->camelcase($handle) . 'WorkflowRequest';
			$wr = call_user_func_array(array($class, 'getByID'), array($this->wrID));
			if (is_object($wr)) {
				$wr->setCurrentWorkflowProgressObject($this);
				return $wr;
			}
		}
	}
	
	/** 
	 * Creates a WorkflowProgress object (which will be assigned to a Page, File, etc... in our system.
	 */
	public static function add($wpCategoryHandle, Workflow $wf, WorkflowRequest $wr) {
		$db = Loader::db();
		$wpDateAdded = Loader::helper('date')->getLocalDateTime();
		$wpCategoryID = $db->GetOne('select wpCategoryID from WorkflowProgressCategories where wpCategoryHandle = ?', array($wpCategoryHandle));
		$db->Execute('insert into WorkflowProgress (wfID, wrID, wpDateAdded, wpCategoryID) values (?, ?, ?, ?)', array(
			$wf->getWorkflowID(), $wr->getWorkflowRequestID(), $wpDateAdded, $wpCategoryID
		));
		$wp = self::getByID($db->Insert_ID());
		$wp->addWorkflowProgressHistoryObject($wr);
		return $wp;
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
		$r = $db->GetRow('select WorkflowProgress.*, WorkflowProgressCategories.wpCategoryHandle from WorkflowProgress inner join WorkflowProgressCategories on WorkflowProgress.wpCategoryID = WorkflowProgressCategories.wpCategoryID where wpID  = ?', array($wpID));
		if (!is_array($r) || (!$r['wpID'])) { 
			return false;
		}
		$class = Loader::helper("text")->camelcase($r['wpCategoryHandle']) . 'WorkflowProgress';
		
		$wp = new $class;
		$wp->setPropertiesFromArray($r);
		$wp->loadDetails();
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
	 * The function that is automatically run when a workflowprogress object is started
	 */
	public function start() {
		$wf = $this->getWorkflowObject();
		if (is_object($wf)) {
			$r = $wf->start($this);
			$this->updateOnAction($wf);
		}
		return $r;
	}
	
	public function updateOnAction(Workflow $wf) {
		$db = Loader::db();
		$num = $wf->getWorkflowProgressCurrentStatusNum($this);
		$time = Loader::helper('date')->getLocalDateTime();
		$db->Execute('update WorkflowProgress set wpDateLastAction = ?, wpCurrentStatus = ? where wpID = ?', array($time, $num, $this->wpID));
	}
	
	/** 
	 * Attempts to run a workflow task on the bound WorkflowRequest object first, then if that doesn't exist, attempts to run 
	 * it on the current WorkflowProgress object
	 * @return WorkflowProgressResponse
	 */
	public function runTask($task, $args = array()) {
		$wf = $this->getWorkflowObject();
		if (in_array($task, $wf->getAllowedTasks())) {
			$wpr = call_user_func_array(array($wf, $task), array($this, $args));
			$this->updateOnAction($wf);
		}
		if (!($wpr instanceof WorkflowProgressResponse)) {
			$wpr = new WorkflowProgressResponse();
		}
		return $wpr;
	}
	
	public function getWorkflowProgressActions() {
		$w = $this->getWorkflowObject();
		$req = $this->getWorkflowRequestObject();
		$actions = $req->getWorkflowRequestAdditionalActions($this);
		$actions = array_merge($actions, $w->getWorkflowProgressActions($this));
		return $actions;
	}
	
	abstract function getWorkflowProgressFormAction();
	abstract function loadDetails();

	public function getWorkflowProgressHistoryObjectByID($wphID) {
		$class = get_class($this) . 'History';
		$db = Loader::db();
		$row = $db->GetRow('select * from WorkflowProgressHistory where wphID = ?', array($wphID));
		if (is_array($row) && ($row['wphID'])) {
			$obj = new $class();
			$obj->setPropertiesFromArray($row);
			$obj->object = @unserialize($row['object']);
			return $obj;
		}
	}
	
	
	public function addWorkflowProgressHistoryObject($obj) {
		$db = Loader::db();
		$db->Execute('insert into WorkflowProgressHistory (wpID, object) values (?, ?)', array($this->wpID, serialize($obj)));
	}

	public function markCompleted() {
		$db = Loader::db();
		$db->Execute('update WorkflowProgress set wpIsCompleted = 1 where wpID = ?', array($this->wpID));
	}
	
	abstract public function getPendingWorkflowProgressList();
	
}
