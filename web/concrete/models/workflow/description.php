<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class WorkflowDescription extends Object {  
	
	public function getText() {
		return $this->text;
	}
	
	public function setText($text) {
		$this->text = $text;
	}

	public function setHTML($html) {
		$this->html = $html;
	}
	
	public function getHTML() {
		return $this->html;
	}
	
	public function setShortStatus($status) {
		$this->status = $status;
	}
	
	public function getShortStatus() {
		return $this->status;
	}
	

}
