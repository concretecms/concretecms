<?php
namespace Concrete\Core\Conversation\FlagType;
use Loader;
use \Concrete\Core\Foundation\Collection\DatabaseItemList;
class List extends DatabaseItemList {

	public function __construct() {
		$this->setQuery('select * from ConversationFlaggedMessageTypes');
		$this->sortBy('cnvMessageFlagTypeID', 'asc');
	}

	public function get() {
		$r = parent::get(0, 0);
		$flagTypes = array();
		foreach($r as $row) {
			$flagTypes[] = FlagType::getByID($row['cnvMessageFlagTypeID']);
		}
		return $flagTypes;
	}

}