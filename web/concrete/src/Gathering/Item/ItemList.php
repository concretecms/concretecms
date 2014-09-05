<?php
namespace Concrete\Core\Gathering\Item;
use Loader;
use Concrete\Core\Legacy\DatabaseItemList;
class ItemList extends DatabaseItemList {

	protected $itemsPerPage = 24;

	public function __construct(Gathering $ag) {
		$this->setQuery('select gaiID from GatheringItems');
		$this->filter('gaID', $ag->getGatheringID());
		$this->filter('gaiIsDeleted', 0);
	}

	public function filterByPublicDate($item, $operator) {
		$this->filter('gaiPublicDateTime', $item, $operator);
	}

	public function sortByDateDescending() {
		$this->sortByMultiple('gaiBatchTimestamp desc', 'gaiBatchDisplayOrder asc');
	}

	public function sortByDateAscending() {
		$this->sortByMultiple('gaiBatchTimestamp asc', 'gaiBatchDisplayOrder desc');
	}

	public function get($itemsToGet = 10000, $offset = 0) {
		$items = array();
		$r = parent::get($itemsToGet, intval($offset));
		foreach($r as $row) {
			$ag = GatheringItem::getByID($row['gaiID']);
			if (is_object($ag)) {
				$items[] = $ag;
			}
		}
		return $items;
	}

}
