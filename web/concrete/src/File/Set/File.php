<?php
namespace Concrete\Core\File\Set;
use FileSet;
use Loader;
use File as ConcreteFile;

class File  {

	public static function getFileSetFiles(FileSet $fs) {
		$db = Loader::db();
		$r = $db->query('select fsfID from FileSetFiles where fsID = ?', array($fs->getFileSetID()));
		$files = array();
		while ($row = $r->FetchRow()) {
			$fsf = static::getByID($row['fsfID']);
			if (is_object($fsf)) {
				$files[] = $fsf;
			}
		}
		return $files;
	}

	public static function createAndGetFile($f_id, $fs_id) {
		$db = Loader::db();
		$fsfID = $db->GetOne('select fsfID from FileSetFiles where fID = ? and fsID = ?', array($f_id, $fs_id));
		if ($fsfID > 0) {
			return static::getByID($fsfID);
		} else {
			$fs = FileSet::getByID($fs_id);
			$f = ConcreteFile::getByID($f_id);
			$fsf = static::add($f, $fs);
			return $fsf;
		}
	}

	public static function add(ConcreteFile $f, FileSet $fs) {
		$db = Loader::db();
		$fsDisplayOrder = $db->GetOne('select count(fID) from FileSetFiles where fsID = ?', array($fs->getFileSetID()));
		$db->insert('FileSetFiles', array('fsID' => $fs->getFileSetID(), 'timestamp' => date('Y-m-d H:i:s'), 'fID' => $f->getFileID(), 'fsDisplayOrder' => $fsDisplayOrder));
		$fsfID = $db->lastInsertId();
		return File::getByID($fsfID);		
	}

	public static function getByID($fsfID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from FileSetFiles where fsfID = ?', array($fsfID));
		if (is_array($r) && $r['fsfID']) {
			$fsf = new static;
			$fsf = array_to_object($fsf, $r);
			return $fsf;
		}
	}

}