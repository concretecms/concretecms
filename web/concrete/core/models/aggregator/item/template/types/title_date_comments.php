<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TitleDateCommentsAggregatorItemTemplate extends AggregatorItemTemplate {

	public function getAggregatorItemTemplateData(AggregatorItem $item) {
		$items = parent::getAggregatorItemTemplateData($item);
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