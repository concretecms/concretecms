<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CollectionAttributeComposerControl extends ComposerControl {
	
	protected $akID;
	protected $cmpControlTypeHandle = 'collection_attribute';
	
	public function setAttributeKeyID($akID) {
		$this->akID = $akID;
		$this->setComposerControlIdentifier($akID);
	}

	public function getAttributeKeyID() {
		return $this->akID;
	}

}