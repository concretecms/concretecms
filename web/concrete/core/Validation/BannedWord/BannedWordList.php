<?php
namespace Concrete\Core\Validation\BannedWord;
use \Concrete\Core\Foundation\Collection\Database\DatabaseItemList;
class BannedWordList extends DatabaseItemList {

	public function __construct() {
		$this->setQuery('select * from BannedWords');
		$this->sortBy('bwID', 'asc');
	}

	public function get() {
		$r = parent::get(0, 0);
		$bannedwords = array();
		foreach($r as $row) {
			$bannedwords[] = BannedWord::getByID($row['bwID']);
		}
		return $bannedwords;
	}

}
