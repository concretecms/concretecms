<?php
namespace Concrete\Core\File\Set;
use Loader;
class SavedSearch extends FileSet {
	
	public static function add($name, $searchRequest, $searchColumnsObject) {
		$fs = parent::createAndGetSet($name, FileSet::TYPE_SAVED_SEARCH);
		$db = Loader::db();
		$v = array($fs->getFileSetID(), serialize($searchRequest), serialize($searchColumnsObject));
		$db->Execute('insert into FileSetSavedSearches (fsID, fsSearchRequest, fsResultColumns) values (?, ?, ?)', $v);
		return $fs;
	}

}


