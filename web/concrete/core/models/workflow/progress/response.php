<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Model_WorkflowProgressResponse extends Object {  
	
	protected $wprURL = '';
	
	public function setWorkflowProgressResponseURL($wprURL) {
		$this->wprURL = $wprURL;
	}

	public function getWorkflowProgressResponseURL() {
		return $this->wprURL;
	}

}
