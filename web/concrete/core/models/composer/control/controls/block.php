<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockComposerControl extends ComposerControl {

	protected $btID;
	protected $cmpControlTypeHandle = 'block';
	
	public function setBlockTypeID($btID) {
		$this->btID = $btID;
		$this->setComposerControlIdentifier($btID);
	}

	public function getBlockTypeID() {
		return $this->btID;
	}


}