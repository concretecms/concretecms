<?php
namespace Concrete\Core\Gathering\Item\Template\Tile;
use \Concrete\Core\Gathering\Item\Template\Tile as TileTemplate;
class TitleDateComments extends TileTemplate {

	public function getGatheringItemTemplateData(GatheringItem $item) {
		$items = parent::getGatheringItemTemplateData($item);
		$totalPosts = 0;
		if (is_array($items['conversation'])) {
			foreach($items['conversation'] as $cnv) {
				$conversation = $cnv->getConversationObject();
				$totalPosts += $conversation->getConversationMessagesTotal();
			}
		} else if (is_object($items['conversation'])) {
			$cnv = $items['conversation'];
			$conversation = $cnv->getConversationObject();
			$totalPosts = $conversation->getConversationMessagesTotal();
		}
		$items['totalPosts'] = $totalPosts;
		return $items;
	}

	
}
