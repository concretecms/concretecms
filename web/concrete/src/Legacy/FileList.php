<?php 
namespace Concrete\Core\Legacy;
use \File as ConcreteFile;
use User;
use FileAttributeKey;

/**
*
* An object that allows a filtered list of files to be returned.
* @package Files
*
*/
class FileList extends DatabaseItemList { 

	protected $attributeFilters = array();
	protected $autoSortColumns = array('fvFilename', 'fvAuthorName','fvTitle', 'fDateAdded', 'fvDateAdded', 'fvSize');
	protected $itemsPerPage = 10;
	protected $attributeClass = 'FileAttributeKey';
	protected $permissionLevel = 'search_file_set';
	protected $filteredFileSetIDs = array();
	
	/* magic method for filtering by attributes. */
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByAttribute($attrib, $a[0]);
			}
		}			
	}

	/** 
	 * Filters by file extension
	 * @param mixed $extension
	 */
	public function filterByExtension($ext) {
		$this->filter('fv.fvExtension', $ext, '=');
	}

	/** 
	 * Filters by type of file
	 * @param mixed $type
	 */
	public function filterByType($type) {
		$this->filter('fv.fvType', $type, '=');
	}
	
	/** 
	 * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
	 */
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$keywordsExact = $db->quote($keywords);
		$qkeywords = $db->quote('%' . $keywords . '%');
		$keys = FileAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		$this->filter(false, '(fvFilename like ' . $qkeywords . ' or fvDescription like ' . $qkeywords . ' or fvTitle like ' . $qkeywords . ' or fvTags like ' . $qkeywords . ' or u.uName = ' . $keywordsExact . $attribsStr . ')');
	}
	

	public function filterBySet($fs) {
		if ($fs != false) {
			$this->filteredFileSetIDs[] = intval($fs->getFileSetID());
		} else {
			// this is what we do when we are filtering by files in NO sets.
			$s1 = FileSet::getMySets();
			$sets = array();
			foreach($s1 as $fs) {
				$sets[] = $fs->getFileSetID();
			}
			if (count($sets) == 0) {
				return false;
			}
			$db = Loader::db();
			$setStr = implode(',', $sets);
			$this->addToQuery("left join FileSetFiles fsfex on fsfex.fID = f.fID");
			$this->filter(false, '(fsfex.fID is null or (select count(fID) from FileSetFiles where fID = fsfex.fID and fsID in (' . $setStr . ')) = 0)');
		}
	}

	public static function export($xml) {
		$fl = new FileList();
		$files = $fl->get();
		if (count($files) > 0) {
			$pkgs = $xml->addChild("files");
			foreach($files as $f) {
				$node = $pkgs->addChild('file');
				$node->addAttribute('filename', $f->getFileName());
			}
		}
	}

	public static function exportArchive($archive) {
		$fl = new FileList();
		$files = $fl->get();
		$fh = Loader::helper('file');
		$filename = $fh->getTemporaryDirectory() . '/' . $archive . '.zip';
		if (count($files) > 0) {
			try {
                $zip = new ZipArchive;
                $res = $zip->open($filename, ZipArchive::CREATE);
                if ($res === TRUE) {
                    foreach($files as $f) {
                        $zip->addFromString($f->getFilename(), $f->getFileContents());
                    }
                    $zip->close();
                    $fh->forceDownload($filename);
                } else {
                    throw new Exception(t('Could not open with ZipArchive::CREATE'));
                }
			} catch(Exception $e) {
				throw new Exception(t('Failed to create zip file as "%s": %s', $filename, $e->getMessage()));
			}
		}
	}
	
	protected function setupFileSetFilters() {	
		$fsIDs = array_unique($this->filteredFileSetIDs);
		$fsIDs = array_filter($fsIDs,'is_numeric');
		
		$db = Loader::db();
		$i = 0;
		$_fsIDs = array();
		if(is_array($fsIDs) && count($fsIDs)) {
			foreach($fsIDs as $fsID) {
				if($fsID > 0) {
					$_fsIDs[] = $fsID;
				}
			}
		}
		
		if (count($_fsIDs) > 1) {
			foreach($_fsIDs as $fsID) {
				if($fsID > 0) {
					if ($i == 0) {
						$this->addToQuery("left join FileSetFiles fsfl on fsfl.fID = f.fID");
					}
					$this->filter(false,'f.fID IN (SELECT DISTINCT fID FROM FileSetFiles WHERE fsID = '.$db->quote($fsID).')');
					$i++;
				}
			}
		} else if (count($_fsIDs) > 0) {
			$this->addToQuery("inner join FileSetFiles fsfl on fsfl.fID = f.fID");
			$this->filter('fsfl.fsID', $fsID);
			$i++;
		}
		
		// add FileSetFiles if we had a file set filter but
		// couldn't add it because it has been removed
		if ($i == 0 && count($this->filteredFileSetIDs)>0) {
			$this->addToQuery("inner join FileSetFiles fsfl on 1=2");
		}		
	}
	
	
	/** 
	 * Filters the file list by file size (in kilobytes)
	 */
	public function filterBySize($from, $to) {
		$this->filter('fv.fvSize', $from * 1024, '>=');
		$this->filter('fv.fvSize', $to * 1024, '<=');
	}
	
	/** 
	 * Filters by public date
	 * @param string $date
	 */
	public function filterByDateAdded($date, $comparison = '=') {
		$this->filter('f.fDateAdded', $date, $comparison);
	}
	
	public function filterByOriginalPageID($ocID) {
		$this->filter('f.ocID', $ocID);
	}
	
	/**
	 * filters a FileList by the uID of the approving User
	 * @param int $uID
	 * @return void
	 * @since 5.4.1.1+
	 */
	public function filterByApproverUID($uID) {
		$this->filter('fv.fvApproverUID', $uID);	
	}
	
	/**
	 * filters a FileList by the uID of the owning User
	 * @param int $uID
	 * @return void
	 * @since 5.4.1.1+
	*/
	public function filterByAuthorUID($uID) {
		$this->filter('fv.fvAuthorUID', $uID);	
	}
	
	public function setPermissionLevel($plevel) {
		$this->permissionLevel = $plevel;
	}
	
	/** 
	 * Filters by tag
	 * @param string $tag
	 */
	public function filterByTag($tag='') { 
		$db=Loader::db();  
		$this->filter(false, "( fv.fvTags like ".$db->qstr("%\n".$tag."\n%")."  )");
	}	
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT DISTINCT f.fID, u.uName as fvAuthorName
		FROM Files f INNER JOIN FileVersions fv ON f.fID = fv.fID 
		LEFT JOIN Users u on u.uID = fv.fvAuthorUID
		');
	}

	protected function setupFilePermissions() {
		$u = new User();
		if ($this->permissionLevel == false || $u->isSuperUser()) {
			return false;
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		foreach($accessEntities as $pae) {
			$peIDs[] = $pae->getAccessEntityID();
		}
		$db = Loader::db();
		// figure out which sets can read files in, not read files in, and read only my files in.
		$fsIDs = $db->GetCol('select fsID from FileSets where fsOverrideGlobalPermissions = 1');
		$viewableSets = array(-1);
		$nonviewableSets = array(-1);
		$myviewableSets = array(-1);

		$owpae = FileUploaderPermissionAccessEntity::getOrCreate();
		
		if (count($fsIDs) > 0) { 
			$pk = PermissionKey::getByHandle($this->permissionLevel);
			foreach($fsIDs as $fsID) {
				$fs = FileSet::getByID($fsID);
				$pk->setPermissionObject($fs);
				$list = $pk->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
				$list = PermissionDuration::filterByActive($list);
				if (count($list) > 0) { 
					foreach($list as $l) {
						$pae = $l->getAccessEntityObject();
						if ($pae->getAccessEntityID() == $owpae->getAccessEntityID()) {
							$myviewableSets[] = $fs->getFileSetID();
						} else {
							if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
								$viewableSets[] = $fs->getFileSetID();
							}
							if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
								$nonviewableSets[] = $fs->getFileSetID();
							}
						}
					}
				} else {
					$nonviewableSets[] = $fs->getFileSetID();
				}
			}
		}

		$fs = FileSet::getGlobal();
		$fk = PermissionKey::getByHandle('search_file_set');
		$fk->setPermissionObject($fs);
		$accessEntities[] = $owpae;
		$list = $fk->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			$pae = $l->getAccessEntityObject();
			if ($pae->getAccessEntityID() == $owpae->getAccessEntityID()) {
				$valid = 'mine';
			} else {
				if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
					$valid = PermissionKey::ACCESS_TYPE_INCLUDE;
				}
				if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
					$valid = PermissionKey::ACCESS_TYPE_EXCLUDE;
				}
			}
		}
		
		$uID = ($u->isRegistered()) ? $u->getUserID() : 0;
		// This excludes all files found in sets where I may only read mine, and I did not upload the file
		$this->filter(false, '(f.uID = ' . $uID . ' or (select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$myviewableSets) . ')) = 0)');		
		
		if ($valid == 'mine') {
			// this means that we're only allowed to read files we've uploaded (unless, of course, those files are in previously covered sets)
			$this->filter(false, '(f.uID = ' . $uID . ' or (select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$viewableSets) . ')) > 0)');		
		}
		
		// this excludes all file that are found in sets that I can't find
		$this->filter(false, '((select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',', $nonviewableSets) . ')) = 0)');		
		
		$uID = ($u->isRegistered()) ? $u->getUserID() : 0;
		// This excludes all files found in sets where I may only read mine, and I did not upload the file
		$this->filter(false, '(f.uID = ' . $uID . ' or (select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$myviewableSets) . ')) = 0)');		

		$db = Loader::db();
		$vpvPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = \'view_file\'');
		if ($this->permissionLevel == 'search_file_set') { 
			$vpPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = \'view_file_in_file_manager\'');
		} else {
			$vpPKID = $vpvPKID;
		}
		$pdIDs = $db->GetCol("select distinct pdID from FilePermissionAssignments fpa inner join PermissionAccessList pal on fpa.paID = pal.paID where pkID in (?, ?) and pdID > 0", array($vpPKID, $vpvPKID));
		$activePDIDs = array();
		if (count($pdIDs) > 0) {
			// then we iterate through all of them and find any that are active RIGHT NOW
			foreach($pdIDs as $pdID) {
				$pd = PermissionDuration::getByID($pdID);
				if ($pd->isActive()) {
					$activePDIDs[] = $pd->getPermissionDurationID();	
				}
			}
		}
		$activePDIDs[] = 0;
		
		// exclude files where its overridden but I don't have the ability to read		
		$this->filter(false, "(f.fOverrideSetPermissions = 0 or (select count(fID) from FilePermissionAssignments fpa inner join PermissionAccessList fpal on fpa.paID = fpal.paID where fpa.fID = f.fID and fpal.accessType = " . PermissionKey::ACCESS_TYPE_INCLUDE . " and fpal.pdID in (" . implode(',', $activePDIDs) . ") and fpal.peID in (" . implode(',', $peIDs) . ") and (if(fpal.peID = " . $owpae->getAccessEntityID() . " and f.uID <> " . $uID . ", false, true)) and (fpa.pkID = " . $vpPKID . ")) > 0)");

		
		// exclude detail files where read is excluded
		$this->filter(false, "f.fID not in (select ff.fID from Files ff inner join FilePermissionAssignments fpaExclude on ff.fID = fpaExclude.fID inner join PermissionAccessList palExclude on fpaExclude.paID = palExclude.paID where fOverrideSetPermissions = 1 and palExclude.accessType = " . PermissionKey::ACCESS_TYPE_EXCLUDE . " and palExclude.pdID in (" . implode(',', $activePDIDs) . ")
			and palExclude.peID in (" . implode(',', $peIDs) . ") and fpaExclude.pkID in (" . $vpPKID . "," . $vpvPKID . "))");		
	}
	
	
	/** 
	 * Returns an array of file objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$files = array();
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$f = ConcreteFile::getByID($row['fID']);			
			$files[] = $f;
		}
		return $files;
	}
	
	public function getTotal(){
		$files = array();
		$this->createQuery();
		return parent::getTotal();
	}
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->filter('fvIsApproved', 1);
			$this->setupAttributeFilters("left join FileSearchIndexAttributes on (fv.fID = FileSearchIndexAttributes.fID)");
			$this->setupFilePermissions();
			$this->setupFileSetFilters();
			$this->queryCreated=1;
		}
	}
	
	//$key can be handle or fak id
	public function sortByAttributeKey($key,$order='asc') {
		$this->sortBy($key, $order); // this is handled natively now
	}
	
	public function sortByFileSetDisplayOrder() {
		$this->sortByMultiple('fsDisplayOrder asc', 'fID asc');
	}

}