<?php
namespace Concrete\Core\Gathering\DataSource;
use Loader;
use \Concrete\Core\Gathering\DataSource\Configuration\Configuration as GatheringDataSourceConfiguration;
class RssFeedDataSource extends DataSource {

	public function createConfigurationObject(Gathering $ag, $post) {
		$o = new RssFeedGatheringDataSourceConfiguration();
		$o->setRssFeedURL($post['rssFeedURL']);
		return $o;
	}

	public function createGatheringItems(GatheringDataSourceConfiguration $configuration) {
		$fp = Loader::helper('feed');
		$feed = $fp->load($configuration->getRssFeedURL(), false); 
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
			$posttime = strtotime($p->get_date('Y-m-d H:i:s'));
			//if ($posttime > $lastupdated) {
				$item = RssFeedGatheringItem::add($configuration, $p);
			//}

			if (is_object($item)) {
				$items[] = $item;
			}
		}
		return $items;
	}
	
}
