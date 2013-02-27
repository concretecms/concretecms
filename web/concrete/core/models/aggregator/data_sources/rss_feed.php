<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorDataSource extends AggregatorDataSource {

	public function createConfigurationObject(Aggregator $ag, $post) {
		$o = new RssFeedAggregatorDataSourceConfiguration();
		$o->setRssFeedURL($post['rssFeedURL']);
		return $o;
	}

	public function getAggregatorItems(AggregatorDataSourceConfiguration $configuration) {
		
	}
	
}