<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
*
* An object that allows a filtered list of files to be returned.
* @package Files
*
*/
class FileList extends DatabaseItemList { 

	private $fileAttributeFilters = array();
	protected $autoSortColumns = array('fvFilename', 'fvAuthorName','fvTitle', 'fDateAdded', 'fvDateAdded', 'fvSize');
	protected $itemsPerPage = 10;
	
	/* magic method for filtering by page attributes. */
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByFileAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByFileAttribute($attrib, $a[0]);
			}
		}			
	}

	/** 
	 * Filters by type of collection (using the ID field)
	 * @param mixed $ctID
	 */
	public function filterByExtension($ext) {
		$this->filter('fv.fvExtension', $ext, '=');
	}

	/** 
	 * Filters by type of collection (using the ID field)
	 * @param mixed $ctID
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
		$keywords = $db->quote('%' . $keywords . '%');
		$this->filter(false, '(fvFilename like ' . $keywords . ' or fvTitle like ' . $keywords . ' or fvTags like ' . $keywords . ' or u.uName = ' . $keywordsExact . ')');
	}
	
	/** 
	 * Filters by files found in a certain set. If "false" is provided, we display files not found in any set. */
	public function filterBySet($fs) {
		if ($fs != false) {
			$tableAliasName='fsf'.intval($fs->getFileSetID());
			$this->addToQuery("left join FileSetFiles {$tableAliasName} on {$tableAliasName}.fID = f.fID");
			$this->filter("{$tableAliasName}.fsID", $fs->getFileSetID(), '=');
		} else {
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
	

	/** 
	 * Filters by tag
	 * @param string $tag
	 */
	public function filterByTag($tag='') { 
		$db=Loader::db();  
		$this->filter(false, "( fv.fvTags like ".$db->qstr("%\n".$tag."\n%")."  )");
	}	
	
	/** 
	 * Filters the list by collection attribute
	 * @param string $handle Collection Attribute Handle
	 * @param string $value
	 */
	public function filterByFileAttribute($handle, $value, $comparison = '=') {
		$ak = FileAttributeKey::getByHandle($handle);
		$this->fileAttributeFilters[] = array($handle, $value, $comparison, $ak->getAttributeKeyType());
	}
	
	/** 
	 * If true, pages will be checked for permissions prior to being returned
	 * @param bool $checkForPermissions
	 */
	public function displayOnlyPermittedPages($checkForPermissions) {
		$this->displayOnlyPermittedPages = $checkForPermissions;
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT DISTINCT f.fID, u.uName as fvAuthorName
		FROM Files f INNER JOIN FileVersions fv ON f.fID = fv.fID 
		LEFT JOIN Users u on u.uID = fv.fvAuthorUID
		');
	}
	
	protected function setupFilePermissions() {
		
		$u = new User();
		if ($u->isSuperUser()) {
			return false;
		}
		$vs = FileSetPermissions::getOverriddenSets('canSearch', FilePermissions::PTYPE_ALL);
		$nvs = FileSetPermissions::getOverriddenSets('canSearch', FilePermissions::PTYPE_NONE);
		$vsm = FileSetPermissions::getOverriddenSets('canSearch', FilePermissions::PTYPE_MINE);
		
		// we remove all the items from nonviewableSets that appear in viewableSets because viewing trumps non-viewing
		
		for ($i = 0; $i < count($nvs); $i++) {
			if (in_array($nvs[$i], $vs)) {
				unset($nvs[$i]);
			}
		}

		// we have $nvs, which is an array of sets of files that we CANNOT see
		// first, we add -1 so that we are always dealing with an array that at least has one value, just for
		// query writing sanity sake
		$nvs[] = -1;
		$vs[] = -1;
		$vsm[] = -1;

		//$this->debug();
		
		// this excludes all file that are found in sets that I can't find
		$this->filter(false, '((select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$nvs) . ')) = 0)');		

		$uID = ($u->isRegistered()) ? $u->getUserID() : 0;
		
		// This excludes all files found in sets where I may only read mine, and I did not upload the file
		$this->filter(false, '(f.uID = ' . $uID . ' or (select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$vsm) . ')) = 0)');		
		
		$fp = FilePermissions::getGlobal();
		if ($fp->getFileSearchLevel() == FilePermissions::PTYPE_MINE) {
			// this means that we're only allowed to read files we've uploaded (unless, of course, those files are in previously covered sets)
			$this->filter(false, '(f.uID = ' . $uID . ' or (select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$vs) . ')) > 0)');		
		}

		// now we filter out files we directly don't have access to
		$groups = $u->getUserGroups();
		$groupIDs = array();
		foreach($groups as $key => $value) {
			$groupIDs[] = $key;
		}
		
		$uID = -1;
		if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}
		
		if (PERMISSIONS_MODEL != 'simple') {
			// There is a really stupid MySQL bug that, if the subquery returns null, the entire query is nullified
			// So I have to do this query OUTSIDE of MySQL and give it to mysql
			$db = Loader::db();
			$fIDs = $db->GetCol("select Files.fID from Files inner join FilePermissions on FilePermissions.fID = Files.fID where fOverrideSetPermissions = 1 and (FilePermissions.gID in (" . implode(',', $groupIDs) . ") or FilePermissions.uID = {$uID}) having max(canSearch) = 0");
			if (count($fIDs) > 0) {
				$this->filter(false, "(f.fID not in (" . implode(',', $fIDs) . "))");
			}			
		}
	}
	
	protected function setupFileAttributeFilters() {
		$db = Loader::db();
		$i = 1;
		foreach($this->fileAttributeFilters as $caf) {
			$fakID = $db->GetOne("select fakID from FileAttributeKeys where akHandle = ?", array($caf[0]));
			$tbl = "fav_{$i}";
			$this->addToQuery("left join FileAttributeValues $tbl on ({$tbl}.fID = fv.fID and fv.fvID = {$tbl}.fvID and {$tbl}.fakID = {$fakID})");
			switch($caf[3]) {
				case 'NUMBER':
					$val = $db->quote($caf[1]);
					$this->filter(false, 'CAST(' . $tbl . '.value as unsigned) ' . $caf[2] . ' ' . $val);
					break;
				case 'DATE':
					$val = $db->quote($caf[1]);
					$this->filter(false, 'CAST(' . $tbl . '.value as date) ' . $caf[2] . ' ' . $val);
					break;
				case 'SELECT_MULTIPLE':
					$multiString = '(';
					$i = 0;
					if(!is_array($caf[1])) $caf[1]=array($caf[1]); 
					foreach($caf[1] as $val) {
						$val = $db->quote('%' . $val . '||%');
						$multiString .= 'REPLACE(' . $tbl . '.value, "\n", "||") like ' . $val . ' ';
						if (($i + 1) < count($caf[1])) {
							$multiString .= 'OR ';
						}
						$i++;
					}
					$multiString .= ')';
					$this->filter(false, $multiString);
					break;
				case 'TEXT':
					$val = $db->quote($caf[1]);
					$this->filter(false, $tbl . '.value ' . $caf[2] . ' ' . $val);
					break;
				default:
					$this->filter($tbl . '.value', $caf[1], $caf[2]);
					break;
			}
			$i++;
		}
	}
	
	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$files = array();
		Loader::model('file');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$f = File::getByID($row['fID']);			
			$files[] = $f;
		}
		return $files;
	}
	
	public function getTotal(){
		$files = array();
		Loader::model('file');
		$this->createQuery();
		return parent::getTotal();
	}
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->filter('fvIsApproved', 1);
			$this->setupFileAttributeFilters();
			$this->setupFilePermissions();
			$this->queryCreated=1;
		}
	}
	
	//$key can be handle or fak id
	public function sortByAttributeKey($key,$order='asc'){
		if(!is_int($key) && intval($key)!=0){
			$fak = FileAttributeKey::getByHandle($key);
			if(!$fak)
				throw new Exception('File list sorting attribute key not found - '.$key );
			$sortFileAttrKeyId=$fak->getAttributeKeyID();
		}else{
			$sortFileAttrKeyId=intval($key);	
		} 
		$this->addToQuery(' left join FileAttributeValues sortAttr on (sortAttr.fID = fv.fID and fv.fvID = sortAttr.fvID and sortAttr.fakID = '.$sortFileAttrKeyId.') ');
		$this->sortBy('sortAttr.value ', $order);	
	} 
	
	public static function getExtensionList() {
		$db = Loader::db();
		$col = $db->GetCol('select distinct(trim(fvExtension)) as extension from FileVersions where fvIsApproved = 1 and fvExtension <> ""');
		return $col;
	}

	public static function getTypeList() {
		$db = Loader::db();
		$col = $db->GetCol('select distinct(trim(fvType)) as type from FileVersions where fvIsApproved = 1 and fvType <> 0');
		return $col;
	}

}
