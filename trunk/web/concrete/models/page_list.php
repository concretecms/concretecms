<?

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
*
* An object that allows a filtered list of pages to be returned.
* @package Pages
*
*/
class PageList extends ItemList {

	private $collectionAttributeFilters = array();
	private $includeSystemPages = false;
	private $displayOnlyPermittedPages = false;
	
	/** 
	 * Sorts this list by display order 
	 */
	public function sortByDisplayOrder() {
		parent::sortBy('cDisplayOrder', 'asc');
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
		$this->filter('Pages.cParentID', $cParentID);
	}
	
	/** 
	 * Filters by type of collection (using the ID field)
	 * @param mixed $ctID
	 */
	public function filterByCollectionTypeID($ctID) {
		$this->filter('Pages.ctID', $ctID);
	}

	/** 
	 * Filters by type of collection (using the handle field)
	 * @param mixed $ctID
	 */
	public function filterByCollectionTypeHandle($ctHandle) {
		$this->filter('PageTypes.ctHandle', $ctHandle);
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
		$this->setQuery('select Pages.cID from Pages inner join PageTypes on (PageTypes.ctID = Pages.ctID) inner join CollectionVersions cv on (cv.cID = Pages.cID and cv.cvIsApproved = 1)');
	}
	
	protected function setupCollectionAttributeFilters() {
		$db = Loader::db();
		foreach($this->collectionAttributeFilters as $caf) {
			$akID = $db->GetOne("select akID from CollectionAttributeKeys where akHandle = ?", array($caf[0]));
			$tbl = "cav_{$akID}";
			$this->addToQuery("left join CollectionAttributeValues $tbl on Pages.cID = {$tbl}.cID and cv.cvID = {$tbl}.cvID");
			$this->filter($tbl . '.value', $caf[1], $caf[2]);
			$this->filter($tbl . '.akID', $akID);
		}
	}
	
	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$pages = array();
		$this->setBaseQuery();
		$this->setupCollectionAttributeFilters();
		$r = parent::execute();
		foreach($r as $row) {
			$nc = Page::getByID($row['cID']);
			$nc->loadVersionObject();
			if ($nc->isSystemPage()) {
				continue;
			}
			$pages[] = $nc;
			if (count($pages) == $itemsToGet) {
				break;
			}
		}
		return $pages;
	}
	
}
