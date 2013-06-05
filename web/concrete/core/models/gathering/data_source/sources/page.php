<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageGatheringDataSource extends GatheringDataSource {

	public function createConfigurationObject(Gathering $ga, $post) {
		$o = new PageGatheringDataSourceConfiguration();
		if ($post['ctID']) {
			$o->setCollectionTypeID($post['ctID']);
		}
		return $o;
	}
	
	public function createGatheringItems(GatheringDataSourceConfiguration $configuration) {
		$pl = new PageList();
		$pl->ignoreAliases();
		$pl->ignorePermissions();
		$gathering = $configuration->getGatheringObject();
		if ($gathering->getGatheringDateLastUpdated()) {
			$pl->filterByPublicDate($gathering->getGatheringDateLastUpdated(), '>');
		}
		$ctID = $configuration->getCollectionTypeID();
		if ($ctID > 0) {
			$pl->filterByCollectionTypeID($ctID);
		}
		$pages = $pl->get();
		$items = array();
		foreach($pages as $c) {
			$item = PageGatheringItem::add($configuration, $c);
			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}

}
