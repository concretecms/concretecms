<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTypeComposerTargetConfiguration extends ComposerTargetConfiguration {

	protected $ctID;

	public function setCollectionTypeID($ctID) {
		$this->ctID = $ctID;
	}

	public function getCollectionTypeID() {
		return $this->ctID;
	}
	
}