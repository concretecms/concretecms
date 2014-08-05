<?php
namespace Concrete\Core\Gathering\Item;
use Loader;
class FlickrFeed extends Item {

	public function loadDetails() {}
	public function canViewGatheringItem() {return true;}

	public static function getListByItem($mixed) {
		$ags = GatheringDataSource::getByHandle('flickr_feed');
		return GatheringItem::getListByKey($ags, $mixed->get_link());
	}
	
	public static function add(GatheringDataSourceConfiguration $configuration, $post) {
		$gathering = $configuration->getGatheringObject();
		try {
			// we wrap this in a try because it MIGHT fail if it's a duplicate
			$item = parent::add($gathering, $configuration->getGatheringDataSourceObject(), $post->get_date('Y-m-d H:i:s'), $post->get_title(), $post->get_link());
		} catch(Exception $e) {}

		if (is_object($item)) {
			$item->assignFeatureAssignments($post);
			$item->setAutomaticGatheringItemTemplate();
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
		$author = $post->get_author();
		if ($author) {
			$this->addFeatureAssignment('author', $author->get_name());
		}
		if ($thumbnail) {
			$this->addFeatureAssignment('image', $thumbnail);
		}
	}

}
