<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorDataSource extends AggregatorDataSource {

	public function createConfigurationObject(Aggregator $ag, $post) {
		$o = new RssFeedAggregatorDataSourceConfiguration();
		$o->setRssFeedURL($post['rssFeedURL']);
		return $o;
	}

	public function createAggregatorItems(AggregatorDataSourceConfiguration $configuration) {
		$fp = Loader::helper('feed');
		$feed = $fp->load($configuration->getRssFeedURL()); 
		$feed->set_item_limit(20);
		$feed->init();
		$feed->handle_content_type();
		$posts = $feed->get_items();

		foreach($posts as $p) {
			$item = RssFeedAggregatorItem::add($configuration, $p);
		}

	}
	
}
