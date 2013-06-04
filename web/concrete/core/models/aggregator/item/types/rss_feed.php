<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorItem extends AggregatorItem {

	public function loadDetails() {}
	public function canViewAggregatorItem() {return true;}

	public static function getListByItem($mixed) {
		$ags = AggregatorDataSource::getByHandle('rss_feed');
		return AggregatorItem::getListByKey($ags, $mixed->get_link());
	}
	
	public static function add(AggregatorDataSourceConfiguration $configuration, $post) {
		$aggregator = $configuration->getAggregatorObject();
		try {
			// we wrap this in a try because it MIGHT fail if it's a duplicate
			$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $post->get_date('Y-m-d H:i:s'), $post->get_title(), $post->get_link());
		} catch(Exception $e) {}

		if (is_object($item)) {
			$item->assignFeatureAssignments($post);
			$item->setAutomaticAggregatorItemTemplate();
			return $item;
		}
	}

	public function assignFeatureAssignments($post) {
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


		$this->addFeatureAssignment('title', $post->get_title());
		$this->addFeatureAssignment('date_time', $post->get_date('Y-m-d H:i:s'));
		$this->addFeatureAssignment('link', $post->get_link());
		$description = strip_tags($post->get_description());
		if ($description != '') {
			$this->addFeatureAssignment('description', $description);
		}
		if ($thumbnail) {
			$this->addFeatureAssignment('image', $thumbnail);
		}
	}

}