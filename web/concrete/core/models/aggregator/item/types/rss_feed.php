<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorItem extends AggregatorItem {

	public function loadDetails() {}

	public static function add(AggregatorDataSourceConfiguration $configuration, $post) {
		$aggregator = $configuration->getAggregatorObject();
		$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $post->get_date('Y-m-d H:i:s'), $post->get_title());
		$db = Loader::db();
		$thumbnail = null;
		$enclosures = $post->get_enclosures();
		if (is_array($enclosures)) {
			foreach($enclosures as $e) {
				if ($e->get_medium() == 'image') {
					$thumbnail = $e->get_link();
					break;
				}
			}
		}


		$item->addFeatureAssignment('title', $post->get_title());
		$item->addFeatureAssignment('date_time', $post->get_date('Y-m-d H:i:s'));
		$item->addFeatureAssignment('link', $post->get_link());
		$item->addFeatureAssignment('description', strip_tags($post->get_description()));
		if ($thumbnail) {
			$item->addFeatureAssignment('image', $thumbnail);
		}
		$item->setDefaultAggregatorItemTemplate();

	}

}