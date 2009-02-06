<?

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
*
* An object that allows a filtered list of files to be returned.
* @package Files
*
*/
class FileList extends DatabaseItemList {

	private $fileAttributeFilters = array();
	protected $autoSortColumns = array('fvFilename', 'fvAuthorName','fvTitle', 'fvDateAdded', 'fvSize');
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
	 * Filters by files found in a certain set */
	public function filterBySet($fs) {
		$this->addToQuery("left join FileSetFiles fsf on fsf.fID = f.fID");
		$this->filter('fsf.fsID', $fs->getFileSetID(), '=');
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
	public function filterByPublicDate($date, $comparison = '=') {
		$this->filter('cv.cvDatePublic', $date, $comparison);
	}
	
	/** 
	 * Filters the list by collection attribute
	 * @param string $handle Collection Attribute Handle
	 * @param string $value
	 */
	public function filterByCollectionAttribute($handle, $value, $comparison = '=') {
		$this->collectionAttributeFilters[] = array($handle, $value, $comparison);
	}
	
	/** 
	 * If true, pages will be checked for permissions prior to being returned
	 * @param bool $checkForPermissions
	 */
	public function displayOnlyPermittedPages($checkForPermissions) {
		$this->displayOnlyPermittedPages = $checkForPermissions;
	}
	
	protected function setBaseQuery() {
		$this->setQuery('select f.fID, u.uName as fvAuthorName from Files f inner join FileVersions fv on f.fID = fv.fID left join Users u on u.uID = fv.fvAuthorUID');
	}
	
	protected function setupFileAttributeFilters() {
		$db = Loader::db();
		foreach($this->fileAttributeFilters as $caf) {
			$akID = $db->GetOne("select akID from FileAttributeKeys where akHandle = ?", array($caf[0]));
			$tbl = "cav_{$akID}";
			$this->addToQuery("left join FileAttributeValues $tbl on {$tbl}.cID = fv.cID and fv.cvID = {$tbl}.cvID");
			$this->filter($tbl . '.value', $caf[1], $caf[2]);
			$this->filter($tbl . '.akID', $akID);
		}
	}
	
	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$files = array();
		Loader::model('file');
		$this->setBaseQuery();
		$this->filter('fvIsApproved', 1);
		$ipp = $this->itemsPerPage; // we store this in case a value is being used for paging that is separate from this value we want to use
		
		$this->setItemsPerPage(0); // no limit
		$this->setupFileAttributeFilters();
		$r = parent::get();
		foreach($r as $row) {
			$f = File::getByID($row['fID']);
			$files[] = $f;
		}
		$this->setItemsPerPage($ipp);
		$this->start = $offset;
		return array_slice($files, $offset, $itemsToGet);
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
