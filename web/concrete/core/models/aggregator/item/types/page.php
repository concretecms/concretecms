<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageAggregatorItem extends AggregatorItem {

	
	public static function add(AggregatorDataSourceConfiguration $configuration, Page $c) {
		$aggregator = $configuration->getAggregatorObject();
		$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $c->getCollectionDatePublic(), $c->getCollectionName());
		$db = Loader::db();
		$db->Execute('insert into agPage (agiID, cID) values (?, ?)', array(
			$item->getAggregatorItemID(),
			$c->getCollectionID()
		));
		$item->addFeatureAssignment('title', $c->getCollectionName());
		$item->addFeatureAssignment('date_time', $c->getCollectionDatePublic());
		$item->addFeatureAssignment('link', Loader::helper('navigation')->getLinkToCollection($c));
		$item->addFeatureAssignment('description', $c->getCollectionDescription());
		if ($c->getAttribute('is_featured')) {
			$item->addFeatureAssignment('featured', 1);
		}
		$assignments = $c->getFeatureAssignments();
		foreach($assignments as $fa) {
			$item->copyFeatureAssignment($fa);
		}
		$item->setAutomaticAggregatorItemTemplate();
	}

	public function duplicate(Aggregator $aggregator) {
		$item = parent::duplicate($aggregator);
		$db = Loader::db();
		$db->Execute('delete from agPage where agiID = ?', array($this->getAggregatorItemID()));
		$db->Execute('insert into agPage (agiID, cID) values (?, ?)', array(
			$this->getAggregatorItemID(), $this->page->getCollectionID()
		));
		return $item;
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