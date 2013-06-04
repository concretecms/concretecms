<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageAggregatorDataSource extends AggregatorDataSource {

	public function createConfigurationObject(Aggregator $ag, $post) {
		$o = new PageAggregatorDataSourceConfiguration();
		if ($post['ctID']) {
			$o->setCollectionTypeID($post['ctID']);
		}
		return $o;
	}
	
	public function createAggregatorItems(AggregatorDataSourceConfiguration $configuration) {
		$pl = new PageList();
		$pl->ignoreAliases();
		$pl->ignorePermissions();
		$aggregator = $configuration->getAggregatorObject();
		if ($aggregator->getAggregatorDateLastUpdated()) {
			$pl->filterByPublicDate($aggregator->getAggregatorDateLastUpdated(), '>');
		}
		$ctID = $configuration->getCollectionTypeID();
		if ($ctID > 0) {
			$pl->filterByCollectionTypeID($ctID);
		}
		$pages = $pl->get();
		$items = array();
		foreach($pages as $c) {
			$item = PageAggregatorItem::add($configuration, $c);
			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}

}