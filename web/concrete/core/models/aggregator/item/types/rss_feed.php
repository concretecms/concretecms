<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorItem extends AggregatorItem {

	public static function add(AggregatorDataSourceConfiguration $configuration, $post) {
		$item = parent::add($configuration->getAggregatorObject(), $configuration->getAggregatorDataSourceObject(), $post->get_date('Y-m-d H:i:s'), $post->get_title());
	}

}