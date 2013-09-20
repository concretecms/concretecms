<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTypePageTypePublishTargetConfiguration extends PageTypePublishTargetConfiguration {

	protected $ctID;

	public function setPageTypeID($ctID) {
		$this->ctID = $ctID;
	}

	public function getPageTypeID() {
		return $this->ctID;
	}

	public function export($cxml) {
		$target = parent::export($cxml);
		$ct = PageType::getByID($this->ctID);
		if (is_object($ct)) {
			$target->addAttribute('pagetype', $ct->getPageTypeHandle());
		}
	}
	
}