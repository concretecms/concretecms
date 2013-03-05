<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TitleDateCommentsAggregatorItemTemplate extends AggregatorItemTemplate {

	public function getAggregatorItemTemplateData(AggregatorItem $item) {
		$items = parent::getAggregatorItemTemplateData($item);
		$totalPosts = 0;
		$conversations = $item->getAggregatorItemExtendedFeatureDetailObjects('conversation');
		foreach($conversations as $cnv) {
			$conversation = $cnv->getConversationObject();
			$totalPosts += $conversation->getConversationMessagesTotal();
		}
		$items['totalPosts'] = $totalPosts;
		return $items;
	}

	
}