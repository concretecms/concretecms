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

	public function getAggregatorItemSizeX() {
		if ($this->page->getAttribute('is_featured')) {
			return 4;
		}

		return 1;
	}
	
	public function getAggregatorItemSizeY() {
		if ($this->page->getAttribute('is_featured')) {
			return 1;
		}
		
		return 1;
	}
}