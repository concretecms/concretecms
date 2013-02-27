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
	
	public function getAggregatorItems(AggregatorDataSourceConfiguration $configuration) {

	}

}