<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FlickrFeedGatheringDataSource extends GatheringDataSource {

	const FLICKR_FEED_URL = 'http://api.flickr.com/services/feeds/photos_public.gne';

	public function createConfigurationObject(Gathering $ag, $post) {
		$o = new FlickrFeedGatheringDataSourceConfiguration();
		$o->setFlickrFeedTags($post['flickrFeedTags']);
		return $o;
	}

	public function createGatheringItems(GatheringDataSourceConfiguration $configuration) {
		$fp = Loader::helper('feed');
		$feed = $fp->load(self::FLICKR_FEED_URL . '?tags=' . $configuration->getFlickrFeedTags(), false); 
		$feed->init();
		$feed->handle_content_type();
		$posts = $feed->get_items(0);

		$gathering = $configuration->getGatheringObject();
		$lastupdated = 0;
		if ($gathering->getGatheringDateLastUpdated()) {
			$lastupdated = strtotime($gathering->getGatheringDateLastUpdated());
		}

		$items = array();
		foreach($posts as $p) {
			$item = FlickrFeedGatheringItem::add($configuration, $p);

			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}
	
}
