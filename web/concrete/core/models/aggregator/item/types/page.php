<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageAggregatorItem extends AggregatorItem {

	public function canViewAggregatorItem() {
		$cp = new Permissions($this->page);
		return $cp->canViewPage();
	}

	public static function getListByItem($mixed) {
		$ags = AggregatorDataSource::getByHandle('page');
		return AggregatorItem::getListByKey($ags, $mixed->getCollectionID());
	}

	public static function add(AggregatorDataSourceConfiguration $configuration, Page $c) {
		$aggregator = $configuration->getAggregatorObject();
		try {
			// we wrap this in a try because it MIGHT fail if it's a duplicate
			$item = parent::add($aggregator, $configuration->getAggregatorDataSourceObject(), $c->getCollectionDatePublic(), $c->getCollectionName(), $c->getCollectionID());
		} catch (Exception $e) {}

		if (is_object($item)) {
			$db = Loader::db();
			$db->Execute('insert into agPage (agiID, cID) values (?, ?)', array(
				$item->getAggregatorItemID(),
				$c->getCollectionID()
			));
			$item->assignFeatureAssignments($c);
			$item->setAutomaticAggregatorItemTemplate();
			return $item;
		}
	}

	public function assignFeatureAssignments($c) {
		$this->addFeatureAssignment('title', $c->getCollectionName());
		$this->addFeatureAssignment('date_time', $c->getCollectionDatePublic());
		$this->addFeatureAssignment('link', Loader::helper('navigation')->getLinkToCollection($c));
		if ($c->getCollectionDescription() != '') {
			$this->addFeatureAssignment('description', $c->getCollectionDescription());
		}
		if ($c->getAttribute('is_featured')) {
			$this->addFeatureAssignment('featured', 1);
		}
		$assignments = $c->getFeatureAssignments();
		foreach($assignments as $fa) {
			$this->copyFeatureAssignment($fa);
		}
	}

	public function duplicate(Aggregator $aggregator) {
		$item = parent::duplicate($aggregator);
		$db = Loader::db();
		$db->Execute('delete from agPage where agiID = ?', array($item->getAggregatorItemID()));
		$db->Execute('insert into agPage (agiID, cID) values (?, ?)', array(
			$item->getAggregatorItemID(), $this->page->getCollectionID()
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