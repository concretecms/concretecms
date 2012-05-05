<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class WorkflowProgress extends Object {  

	protected $wpID;
	
	/** 
	 * Gets the ID of the progress object
	 */
	public function getWorkflowProgressID() {return $this->wpID;}
	
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

	
	
}
