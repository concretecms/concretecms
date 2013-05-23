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
		$feed->init();
		$feed->handle_content_type();
		$posts = $feed->get_items(0);

		$aggregator = $configuration->getAggregatorObject();
		$lastupdated = 0;
		if ($aggregator->getAggregatorDateLastUpdated()) {
			$lastupdated = strtotime($aggregator->getAggregatorDateLastUpdated());
		}

		$items = array();
		foreach($posts as $p) {
			$posttime = strtotime($p->get_date('Y-m-d H:i:s'));
			//if ($posttime > $lastupdated) {
				$item = RssFeedAggregatorItem::add($configuration, $p);
			//}

			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}
	
}
