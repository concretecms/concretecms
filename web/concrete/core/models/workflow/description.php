<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Model_WorkflowDescription extends Object {  

	public function getDescription() {
		return $this->text;
	}
	
	public function setDescription($text) {
		$this->text = $text;
	}
	
	public function getEmailDescription() {
		return $this->emailtext;
	}
	
	public function setEmailDescription($text) {
		$this->emailtext = $text;
	}

	public function setInContextDescription($html) {
		$this->incontext = $html;
	}
	
	public function getInContextDescription() {
		return $this->incontext;
	}
	
	public function setShortStatus($status) {
		$this->status = $status;
	}
	
	public function getShortStatus() {
		return $this->status;
	}
	

}
