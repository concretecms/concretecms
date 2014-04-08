<?php
namespace Concrete\Core\Gathering\DataSource;
use Loader;
use \Concrete\Core\Gathering\DataSource\Configuration\Configuration as GatheringDataSourceConfiguration;
class TwitterDataSource extends DataSource {

	const TWITTER_SEARCH_URL = 'http://api.flickr.com/services/feeds/photos_public.gne';

	public function createConfigurationObject(Gathering $ag, $post) {
		$o = new TwitterGatheringDataSourceConfiguration();
		$o->setTwitterUsername($post['twitterUsername']);
		return $o;
	}

	public function createGatheringItems(GatheringDataSourceConfiguration $configuration) {
		
		$connection = new TwitterOAuth(TWITTER_APP_CONSUMER_KEY, TWITTER_APP_CONSUMER_SECRET, TWITTER_APP_ACCESS_TOKEN, TWITTER_APP_ACCESS_SECRET);
		$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$configuration->getTwitterUsername()."&count=50");
		if (!empty($tweets->errors[0])) {
			throw new Exception($tweets->errors[0]->message);
		}
		
		$gathering = $configuration->getGatheringObject();
		$lastupdated = 0;
		if ($gathering->getGatheringDateLastUpdated()) {
			$lastupdated = strtotime($gathering->getGatheringDateLastUpdated());
		}

		$items = array();
		foreach($tweets as $tweet) {
			$item = TwitterGatheringItem::add($configuration, $tweet);

			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}
	
}
