<?php
namespace Concrete\Core\File\Set;
use Loader;
class File  {
	public static function createAndGetFile($f_id, $fs_id){	
		$file_set_file = new static();
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