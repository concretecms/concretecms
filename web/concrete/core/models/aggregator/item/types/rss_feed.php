<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_RssFeedAggregatorItem extends AggregatorItem implements TitleFeatureInterface, DateTimeFeatureInterface, LinkFeatureInterface, BodyFeatureInterface {

	protected $features = array(
		'title', 'date_time', 'link', 'body'
	);

	public function getAggregatorItemExtendedFeatures() {
		return array();
	}
	
	public function getAggregatorItemExtendedFeatureDetailObjects($feHandle) {
		return false;
	}

	public function getFeatureDataTitle() {
		return $this->title;
	}

	public function getFeatureDataDateTime() {
		return $this->datetime;
	}
	
	public function getFeatureDataLink() {
		return $this->url;
	}

	public function getFeatureDataBody() {
		return $this->description;
	}

	public function getFeatureDataDescription() {return $this->description;}

	public function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select title, datetime, description, url from agRssFeed where agiID = ?', array($this->getAggregatorItemID()));
		$this->setPropertiesFromArray($row);
	}

	public static function add(AggregatorDataSourceConfiguration $configuration, $post) {
		$aggregator = $configuration->getAggregatorObject();
		$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $post->get_date('Y-m-d H:i:s'), $post->get_title());
		$db = Loader::db();
		$db->Execute('insert into agRssFeed (agiID, title, datetime, description, url) values (?, ?, ?, ?, ?)', array(
			$item->getAggregatorItemID(),
			$post->get_title(),
			$post->get_date('Y-m-d H:i:s'),
			$post->get_description(),
			$post->get_link()
		));
		$item->setDefaultAggregatorItemTemplate();

	}

	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from agRssFeed where agiID = ?', array($this->getAggregatorItemID()));
	}

}