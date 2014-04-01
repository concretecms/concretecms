<?php
namespace Concrete\Core\File\Set;
use \Concrete\Core\Foundation\Collection\DatabaseItemList;
class FileSetList extends DatabaseItemList {

	protected $itemsPerPage = 10;
	
	public function filterByKeywords($kw) {
		$db = Loader::db();
		$this->filter(false, "(FileSets.fsName like " . $db->qstr('%' . $kw . '%') . ")");
	}
	
	function __construct() {
		$this->setQuery("select FileSets.fsID from FileSets");
		$this->sortBy('fsName', 'asc');
	}
	
	public function filterByType($fsType) {
		switch($fsType) {
			case FileSet::TYPE_PRIVATE:
				$u = new User();
				$this->filter('FileSets.uID', $u->getUserID());
				break;
		}
		$this->filter('FileSets.fsType', $fsType);
	}
	
	public function get($itemsToGet = 0, $offset = 0) {
		$sets = array();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$fs = FileSet::getByID($row['fsID']);
			if (is_object($fs)) {
				$sets[] = $fs;
			}
		}
		return $sets;
	}

}