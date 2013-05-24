<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorItem extends AggregatorItem {

	public function loadDetails() {}
	public function canViewAggregatorItem() {return true;}
	
	public static function add(AggregatorDataSourceConfiguration $configuration, $post) {
		$aggregator = $configuration->getAggregatorObject();
		try {
			// we wrap this in a try because it MIGHT fail if it's a duplicate
			$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $post->get_date('Y-m-d H:i:s'), $post->get_title(), $post->get_link());
		} catch(Exception $e) {}

		if (is_object($item)) {
			$db = Loader::db();
			$thumbnail = null;
			$enclosures = $post->get_enclosures();
			if (is_array($enclosures)) {
				foreach($enclosures as $e) {
					if ($e->get_medium() == 'image' || strpos($e->get_type(), 'image') === 0) {
						$thumbnail = $e->get_link();
						break;
					}
				}
			}


			$item->addFeatureAssignment('title', $post->get_title());
			$item->addFeatureAssignment('date_time', $post->get_date('Y-m-d H:i:s'));
			$item->addFeatureAssignment('link', $post->get_link());
			$description = strip_tags($post->get_description());
			if ($description != '') {
				$item->addFeatureAssignment('description', $description);
			}
			if ($thumbnail) {
				$item->addFeatureAssignment('image', $thumbnail);
			}
			$item->setAutomaticAggregatorItemTemplate();
			return $item;
		}
	}

}