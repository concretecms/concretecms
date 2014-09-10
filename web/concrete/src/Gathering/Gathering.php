<?php
namespace Concrete\Core\Gathering;
use Loader;
use \Concrete\Core\Foundation\Object;
class Gathering extends Object implements \Concrete\Core\Permission\ObjectInterface {

	public function getGatheringID() {return $this->gaID;}
	public function getGatheringDateCreated() {return $this->gaDateCreated;}
	public function getGatheringDateLastUpdated() {return $this->gaDateLastUpdated;}
	public function getPermissionObjectIdentifier() { return $this->gaID;}


	public function getPermissionResponseClassName() {
		return '\\Concrete\\Core\\Permission\\Response\\GatheringResponse';
	}

	public function getPermissionAssignmentClassName() {
		return '\\Concrete\\Core\\Permission\\Assignment\\GatheringAssignment';
	}
	public function getPermissionObjectKeyCategoryHandle() {
		return 'gathering';
	}

	public static function getByID($gaID) {
		$db = Loader::db();
		$r = $db->GetRow('select gaID, gaDateCreated, gaDateLastUpdated from Gatherings where gaID = ?', array($gaID));
		if (is_array($r) && $r['gaID'] == $gaID) {
			$ag = new Gathering;
			$ag->setPropertiesFromArray($r);
			return $ag;
		}
	}

	public static function getList() {
		$db = Loader::db();
		$r = $db->Execute('select gaID from Gatherings order by gaDateLastUpdated asc');
		$gatherings = array();
		while ($row = $r->FetchRow()) {
			$ag = Gathering::getByID($row['gaID']);
			if (is_object($ag)) {
				$gatherings[] = $ag;
			}
		}
		return $gatherings;
	}

	public static function add() {
		$db = Loader::db();
		$date = Loader::helper('date')->getOverridableNow();
		$r = $db->Execute('insert into Gatherings (gaDateCreated) values (?)', array($date));
		return Gathering::getByID($db->Insert_ID());
	}


	public function getGatheringItems() {
		$db = Loader::db();
		$r = $db->Execute('select gaiID from GatheringItems where gaID = ?', array($this->gaID));
		$list = array();
		while ($row = $r->FetchRow()) {
			$item = GatheringItem::getByID($row['gaiID']);
			if (is_object($item)) {
				$list[] = $item;
			}
		}
		return $list;
	}

	public function getConfiguredGatheringDataSources() {
		$db = Loader::db();
		$r = $db->Execute('select gcsID from GatheringConfiguredDataSources where gaID = ?', array($this->gaID));
		$list = array();
		while ($row = $r->FetchRow()) {
			$source = GatheringDataSourceConfiguration::getByID($row['gcsID']);
			if (is_object($source)) {
				$list[] = $source;
			}
		}
		return $list;
	}

	public function clearConfiguredGatheringDataSources() {
		$sources = $this->getConfiguredGatheringDataSources();
		foreach($sources as $s) {
			$s->delete();
		}
	}

	public function duplicate() {
		$db = Loader::db();
		$newag = Gathering::add();
		// dupe data sources
		foreach($this->getConfiguredGatheringDataSources() as $source) {
			$source->duplicate($newag);
		}
		// dupe items
		foreach($this->getGatheringItems() as $item) {
			$item->duplicate($newag);
		}
		return $newag;
	}

	/**
	 * Runs through all active gathering data sources, creates GatheringItem objects
	 */
	public function generateGatheringItems() {
		$configuredDataSources = $this->getConfiguredGatheringDataSources();
		$items = array();
		foreach($configuredDataSources as $configuration) {
			$dataSource = $configuration->getGatheringDataSourceObject();
			$dataSourceItems = $dataSource->createGatheringItems($configuration);
			$items = array_merge($dataSourceItems, $items);
		}

		// now we loop all the items returned, and assign the batch to those items.
		$agiBatchTimestamp = time();
		$db = Loader::db();
		foreach($items as $it) {
			$it->setGatheringItemBatchTimestamp($agiBatchTimestamp);
			$it->setAutomaticGatheringItemSlotWidth();
			$it->setAutomaticGatheringItemSlotHeight();
		}

		// now, we find all the items with that timestamp, and we update their display order.
		$agiBatchDisplayOrder = 0;
		$r = $db->Execute('select gaiID from GatheringItems where gaID = ? and gaiBatchTimestamp = ? order by gaiPublicDateTime desc', array($this->getGatheringID(), $agiBatchTimestamp));
		while ($row = $r->FetchRow()) {
			$db->Execute('update GatheringItems set gaiBatchDisplayOrder = ? where gaiID = ?', array($agiBatchDisplayOrder, $row['gaiID']));
			$agiBatchDisplayOrder++;
		}

		$date = Loader::helper('date')->getOverridableNow();
		$db->Execute('update Gatherings set gaDateLastUpdated = ? where gaID = ?', array($date, $this->gaID));

	}

	public function clearGatheringItems() {
		$items = $this->getGatheringItems();
		foreach($items as $it) {
			$it->delete();
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from Gatherings where gaID = ?', array($this->getGatheringID()));
		$this->clearConfiguredGatheringDataSources();
		$this->clearGatheringItems();
	}

}
