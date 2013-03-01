<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageAggregatorDataSourceConfiguration extends AggregatorDataSourceConfiguration {
	
	protected $ctIDs = array();

	public function setCollectionTypeIDs($ctIDs) {
		$this->ctIDs = $ctIDs;
	}

	public function getCollectionTypeIDs() {
		return $this->ctIDs;
	}

}