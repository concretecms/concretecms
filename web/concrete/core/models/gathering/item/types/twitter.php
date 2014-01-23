<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TwitterGatheringItem extends GatheringItem {

	public function loadDetails() {}
	public function canViewGatheringItem() {return true;}

	public static function getListByItem($mixed) {
		$ags = GatheringDataSource::getByHandle('twitter');
		return GatheringItem::getListByKey($ags, $mixed->get_link());
	}
	
	public static function add(GatheringDataSourceConfiguration $configuration, $tweet) {
		$gathering = $configuration->getGatheringObject();
		try {
			// we wrap this in a try because it MIGHT fail if it's a duplicate
			$item = parent::add($gathering, $configuration->getGatheringDataSourceObject(), date('Y-m-d H:i:s', strtotime($tweet->created_at)), $tweet->text, $tweet->id);
		} catch(Exception $e) {}

		if (is_object($item)) {
			$item->assignFeatureAssignments($tweet);
			$item->setAutomaticGatheringItemTemplate();
			return $item;
		}
	}

	public function assignFeatureAssignments($tweet) {
		$this->addFeatureAssignment('tweet', $tweet->text);
		$this->addFeatureAssignment('date_time', $tweet->created_at);
	}

}
