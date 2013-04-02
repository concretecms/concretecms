<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_ParentPageComposerTargetConfiguration extends ComposerTargetConfiguration {

	protected $cParentID;
	
	public function setParentPageID($cParentID) {
		$this->cParentID = $cParentID;
	}

	public function getParentPageID() {
		return $this->cParentID;
	}

	public function getComposerConfiguredTargetParentPageID() {
		return $this->cParentID;
	}

}