<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageAggregatorItem extends AggregatorItem implements TitleFeatureInterface, DateTimeFeatureInterface, LinkFeatureInterface {

	public function getAggregatorItemExtendedFeatures() {
		$assignments = $this->page->getFeatureAssignments();
		$features = array();
		foreach($assignments as $as) {
			$detail = $as->getFeatureDetailObject();
			$features[] = $detail->getFeatureHandle();
		}
		return $features;
	}

	protected $features = array(
		'title', 'date_time', 'link'
	);

	public function getFeatureDataTitle() {
		return $this->page->getCollectionName();
	}

	public function getFeatureDataDateTime() {
		return $this->page->getCollectionDatePublic();
	}
	
	public function getFeatureDataLink() {
		return Loader::helper('navigation')->getLinkToCollection($this->page);
	}

	public function getAggregatorItemExtendedFeatureDetailObjects($feHandle) {
		$assignments = $this->page->getFeatureAssignments();
		$objects = array();
		foreach($assignments as $as) {
			$detail = $as->getFeatureDetailObject();
			if ($detail->getFeatureHandle() == $feHandle) {
				$objects[] = $detail;
			}
		}
		return $objects;
	}
	
	public static function add(AggregatorDataSourceConfiguration $configuration, Page $c) {
		$aggregator = $configuration->getAggregatorObject();
		$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $c->getCollectionDatePublic(), $c->getCollectionName());
		$db = Loader::db();
		$db->Execute('insert into agPage (agiID, cID) values (?, ?)', array(
			$item->getAggregatorItemID(),
			$c->getCollectionID()
		));
		$item->setDefaultAggregatorItemTemplate();
	}


	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from agPage where agiID = ?', array($this->getAggregatorItemID()));
	}

	public function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select cID from agPage where agiID = ?', array($this->getAggregatorItemID()));
		$this->setPropertiesFromArray($row);
		$this->page = Page::getByID($row['cID'], 'ACTIVE');
	}

	public function getCollectionObject() {
		return $this->page;
	}
	


}