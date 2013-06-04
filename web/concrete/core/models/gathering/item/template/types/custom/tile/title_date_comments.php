<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TitleDateCommentsTileGatheringItemTemplate extends TileGatheringItemTemplate {

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
