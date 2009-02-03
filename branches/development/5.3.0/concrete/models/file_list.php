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
	 * Sorts this list by display order 
	 */
	public function sortByDisplayOrder() {
		parent::sortBy('p1.cDisplayOrder', 'asc');
	}
	
	/** 
	 * Sorts this list by display order descending 
	 */
	public function sortByDisplayOrderDescending() {
		parent::sortBy('cDisplayOrder', 'desc');
	}
	
	/** 
	 * Sorts this list by public date ascending order 
	 */
	public function sortByPublicDate() {
		parent::sortBy('cvDatePublic', 'asc');
	}
	
	/** 
	 * Sorts this list by name 
	 */
	public function sortByName() {
		parent::sortBy('cvName', 'asc');
	}
	
	/** 
	 * Sorts this list by name descending order
	 */
	public function sortByNameDescending() {
		parent::sortBy('cvName', 'desc');
	}

	/** 
	 * Sorts this list by public date descending order 
	 */
	public function sortByPublicDateDescending() {
		parent::sortBy('cvDatePublic', 'desc');
	}	
	
	/** 
	 * Sets the parent ID that we will grab pages from. 
	 * @param mixed $cParentID
	 */
	public function filterByParentID($cParentID) {
		$this->filter('p1.cParentID', $cParentID);
	}
	
	/** 
	 * Filters by type of collection (using the ID field)
	 * @param mixed $ctID
	 */
	public function filterByCollectionTypeID($ctID) {
		$this->filter(false, "(p1.ctID = $ctID or p2.ctID = $ctID)");
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
		$this->setQuery('select f.fID from Files f inner join FileVersions fv on f.fID = fv.fID');
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
		$this->setItemsPerPage(0); // no limit
		$this->setupFileAttributeFilters();
		$r = parent::get();
		foreach($r as $row) {
			$f = File::getByID($row['fID']);
			$files[] = $f;
		}
		return $files;
	}
	
}
