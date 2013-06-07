<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FlickrFeedGatheringDataSourceConfiguration extends GatheringDataSourceConfiguration  {

	public function setFlickrFeedTags($tags) {
		$this->tags = $tags;
	}

	public function getFlickrFeedTags() {
		return $this->tags;
	}

}
