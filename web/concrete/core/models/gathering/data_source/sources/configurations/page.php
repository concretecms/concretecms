<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageGatheringDataSourceConfiguration extends GatheringDataSourceConfiguration {
	
	protected $ctID;

	public function setCollectionTypeID($ctID) {
		$this->ctID = $ctID;
	}

	public function getCollectionTypeID() {
		return $this->ctID;
	}

}
