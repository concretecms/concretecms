<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorItem extends AggregatorItem {

	public function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select title, description, url from agRssFeed where agiID = ?', array($this->getAggregatorItemID()));
		$this->setPropertiesFromArray($row);
	}

	public static function add(AggregatorDataSourceConfiguration $configuration, $post) {
		$aggregator = $configuration->getAggregatorObject();
		$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $post->get_date('Y-m-d H:i:s'), $post->get_title());
		$db = Loader::db();
		$db->Execute('insert into agRssFeed (agiID, title, description, url) values (?, ?, ?, ?)', array(
			$item->getAggregatorItemID(),
			$post->get_title(),
			$post->get_description(),
			$post->get_link()
		));
	}

	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from agRssFeed where agiID = ?', array($this->getAggregatorItemID()));
	}

	public function getTitle() {return $this->title;}
	public function getDescription() {return $this->description;}
	public function getURL() {return $this->url;}


}