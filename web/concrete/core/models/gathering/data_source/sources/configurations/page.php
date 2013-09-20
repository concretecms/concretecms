<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageGatheringDataSourceConfiguration extends GatheringDataSourceConfiguration {
	
	protected $ptID;

	public function setPageTypeID($ptID) {
		$this->ptID = $ptID;
	}

	public function getPageTypeID() {
		return $this->ptID;
	}

}
