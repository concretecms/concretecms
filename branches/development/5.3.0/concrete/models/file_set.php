<?php
	class FileSet extends Model {
		const TYPE_PRIVATE 	= 0;
		const TYPE_PUBLIC 	= 1;
		const TYPE_STARRED 	= 2;
	
		public function getMySets($u = false) {
			if ($u == false) {
				$u = new User();
			}
			$db = Loader::db();
			$sets = array();
			$r = $db->Execute('select * from FileSets where fsType = ? or (fsType = ? and uID = ?) order by fsName asc', array(FileSet::TYPE_PUBLIC, FileSet::TYPE_PRIVATE, $u->getUserID()));
			while ($row = $r->FetchRow()) {
				$fs = new FileSet();
				foreach($row as $key => $value) {
					$fs->{$key} = $value;
				}
				$sets[] = $fs;
			}
			return $sets;
		}
		
		public function getByID($fsID) {
			$db = Loader::db();
			$row = $db->GetRow('select * from FileSets where fsID = ?', array($fsID));
			if (is_array($row)) {
				$fs = new FileSet();
				foreach($row as $key => $value) {
					$fs->{$key} = $value;
				}
				return $fs;
			}
		}
		
		public function getFileSetID() {return $this->fsID;}
		public function getFileSetName() {return $this->fsName;}
		
			
	}