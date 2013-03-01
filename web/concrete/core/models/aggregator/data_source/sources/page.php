<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageAggregatorDataSource extends AggregatorDataSource {

	public function createConfigurationObject(Aggregator $ag, $post) {
		$o = new PageAggregatorDataSourceConfiguration();
		if (is_array($post['ctIDs'])) {
			$o->setCollectionTypeIDs($post['ctIDs']);
		}
		return $o;
	}
	
	public function createAggregatorItems(AggregatorDataSourceConfiguration $configuration) {
		$pl = new PageList();
		$pl->ignoreAliases();
		$pl->ignorePermissions();
		$ctIDs = $configuration->getCollectionTypeIDs();
		if (count($ctIDs) > 0) {
			$pl->filterByCollectionTypeID($ctIDs);
		}
		$pages = $pl->get();
		foreach($pages as $c) {
			$item = PageAggregatorItem::add($configuration, $c);
		}

	}

}