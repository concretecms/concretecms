<?php
	class FileSet extends Model {
		const TYPE_PRIVATE 	= 0;
		const TYPE_PUBLIC 	= 1;
		const TYPE_STARRED 	= 2;
		protected $fileSetFiles;
	
		public function getMySets($u = false) {
			if ($u == false) {
				$u = new User();
			}
			$db = Loader::db();
			$sets = array();
			$r = $db->Execute('select * from FileSets where fsType = ? or (fsType in (?, ?) and uID = ?) order by fsName asc', array(FileSet::TYPE_PUBLIC, FileSet::TYPE_STARRED, FileSet::TYPE_PRIVATE, $u->getUserID()));
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
		
		/**
		 * Creats a new fileset if set doesn't exists
		 *
		 * If we find a multiple groups with the same properties,
		 * we return an array containing each group
		 * @param string $fs_name
		 * @param int $fs_type
		 * @param int $fs_uid
		 * @return Mixed 
		 */		
		public static function createAndGetSet($fs_name, $fs_type, $fs_uid=false) {
			if (!$fs_uid) {
				$u = new User();
				$fs_uid = $u->uID;
			}
			
			$file_set = new FileSet();
			$criteria = array($fs_name,$fs_type,$fs_uid);
			$matched_sets = $file_set->Find('fsName=? AND fsType=? and uID=?',$criteria);
			
			if (1 === count($matched_sets) ) {
				return $matched_sets[0];
			}
			else if (1 < count($matched_sets)) {
				return $matched_sets;
			}
			else{
				$file_set->fsName 	= $fs_name;
				$file_set->fsType 	= $fs_type;
				$file_set->uID		= $fs_uid;
				$file_set->save();
				return $file_set;
			}			
		}
		
		/**
		* Adds the file to the set
		* @param type $fID  //accepts an ID or a File object
		* @return object
		*/		
		public function AddFileToSet($f_id) {
			if (is_object($f_id)) {
				$f_id = $f_id->fID;
			}			
			$file_set_file = FileSetFile::createAndGetFile($f_id,$this->fsID);
			return $file_set_file;
		}
		
		public function RemoveFileFromSet($f_id){
			if (is_object($f_id)) {
				$f_id = $f_id->fID;
			}			
			$file_set_file = FileSetFile::createAndGetFile($f_id,$this->fsID);
			$file_set_file->Delete();
		}

		/**
		* Get a list of files asociated with this set
		*
		* Can obsolete this when we get version of ADOdB with one/many support
		* @return type $var_name
		*/		
		private function populateFiles(){			
			$utility 			= new FileSetFile();
			$this->fileSetFiles = $utility->Find('fsID=?',array($this->fsID));
		}
		
		public function hasFileID($f_id){
			if (!is_array($this->fileSetFiles)) {
				$this->populateFiles();
			}			
			foreach ($this->fileSetFiles as $file) {
				if($file->fID == $f_id){
					return true;
				}
			}
		}		
	}
	class FileSetFile extends Model {
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
				$file_set_file->fID =  $f_id;			
				$file_set_file->fsID = $fs_id;
				$file_set_file->Save();
				return $file_set_file;
			}			
		}
	}
	
	