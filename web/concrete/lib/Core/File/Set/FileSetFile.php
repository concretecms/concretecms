<?php
namespace Concrete\Core\File\Set;
class FileSetFile  {
	public static function createAndGetFile($f_id, $fs_id){	
		$file_set_file = new FileSetFile();
		$criteria = array($f_id,$fs_id);		
		
		$matched_sets = $file_set_file->Find('fID=? AND fsID=?',$criteria);
		
		if (1 === count($matched_sets) ) {
			return $matched_sets[0];
		}
		else if (1 < count($matched_sets)) {
			return $matched_sets;
		}
		else{
			//AS: Adodb Active record is complaining a ?/value array mismatch unless
			//we explicatly set the primary key ID field to null
			$db = Loader::db();
			$fsDisplayOrder = $db->GetOne('select count(fID) from FileSetFiles where fsID = ?', $fs_id);
			$file_set_file->fsfID = null;
			$file_set_file->fID =  $f_id;			
			$file_set_file->fsID = $fs_id;
			$file_set_file->timestamp = null;
			$file_set_file->fsDisplayOrder = $fsDisplayOrder;
			$file_set_file->Save();
			return $file_set_file;
		}			
	}
}
	
	class Concrete5_Model_FileSetSavedSearch extends Concrete5_Model_FileSet {
		
		public static function add($name, $searchRequest, $searchColumnsObject) {
			$fs = parent::createAndGetSet($name, FileSet::TYPE_SAVED_SEARCH);
			$db = Loader::db();
			$v = array($fs->getFileSetID(), serialize($searchRequest), serialize($searchColumnsObject));
			$db->Execute('insert into FileSetSavedSearches (fsID, fsSearchRequest, fsResultColumns) values (?, ?, ?)', $v);
			return $fs;
		}
	
	}
	
	
