<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TwitterGatheringDataSourceConfiguration extends GatheringDataSourceConfiguration  {

	public function setTwitterUsername($username) {
		$this->username = $username;
	}

	public function getTwitterUsername() {
		return $this->username;
	}

}
