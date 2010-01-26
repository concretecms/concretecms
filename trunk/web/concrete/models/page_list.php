<?

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
*
* An object that allows a filtered list of pages to be returned.
* @package Pages
*
*/
class PageList extends DatabaseItemList {

	private $includeSystemPages = false;
	protected $attributeFilters = array();
	private $displayOnlyPermittedPages = false;
	private $displayOnlyApprovedPages = true;
	private $systemPagesToExclude = array('login.php', 'register.php', 'download_file.php', 'profile/%', 'dashboard/%');
	private $filterByCParentID = 0;
	private $filterByCT = false;
	private $ignorePermissions = false;
	protected $attributeClass = 'CollectionAttributeKey';
	protected $autoSortColumns = array('cvName', 'cvDatePublic', 'cDateAdded', 'cDateModified');
	
	/* magic method for filtering by page attributes. */
	
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
	
	public function ignorePermissions() {
		$this->ignorePermissions = true;
	}
	
	public function displayUnapprovedPages() {
		$this->displayOnlyApprovedPages = false;
	}

	/** 
	 * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
	 */
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$kw = $db->quote($keywords);
		$qk = $db->quote('%' . $keywords . '%');
		Loader::model('attribute/categories/collection');		
		$keys = CollectionAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		$this->filter(false, "(psi.cName like {$qk} or psi.cDescription like {$qk} or (match(psi.cName, psi.cDescription, psi.content) against ({$kw} in boolean mode))
		{$attribsStr})");
	}

	public function filterByName($name, $exact = false) {
		if ($exact) {
			$this->filter('cvName', $name, '=');
		} else {
			$this->filter('cvName', '%' . $name . '%', 'like');
		}	
	}
	
	public function filterByPath($path, $includeAllChildren = true) {
		if (!$includeAllChildren) {
			$this->filter('PagePaths.cPath', $path, '=');
		} else {
			$this->filter('PagePaths.cPath', $path . '/%', 'like');
		}	
		$this->filter('PagePaths.ppIsCanonical', 1);
	}
	
	/** 
	 * Sets up a list to only return items the proper user can access 
	 */
	public function setupPermissions() {
		$u = new User();
		if ($u->isSuperUser() || ($this->ignorePermissions)) {
			return; // super user always sees everything. no need to limit
		}
		
		$groups = $u->getUserGroups();
		$groupIDs = array();
		foreach($groups as $key => $value) {
			$groupIDs[] = $key;
		}
		
		$uID = -1;
		if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}
		
		$date = Loader::helper('date')->getLocalDateTime();

		if (PERMISSIONS_MODEL != 'simple') {
			// support timed release
			$this->filter(false, "((select count(cID) from PagePermissions pp1 where pp1.cID = if(p2.cID is null, p1.cInheritPermissionsFromCID, p2.cInheritPermissionsFromCID) and
				pp1.cgPermissions like 'r%' and (
					(pp1.gID in (" . implode(',', $groupIDs) . ") or pp1.uID = {$uID})
					and 
						(pp1.cgStartDate is null or pp1.cgStartDate <= '{$date}')
					and 
						(pp1.cgEndDate is null or pp1.cgEndDate >= '{$date}')
				)) > 0 or (p1.cPointerExternalLink !='' AND p1.cPointerExternalLink IS NOT NULL ))");
		} else {
			$this->filter(false, "((select count(cID) from PagePermissions pp1 where pp1.cID = if(p2.cID is null, p1.cInheritPermissionsFromCID, p2.cInheritPermissionsFromCID) and pp1.cgPermissions like 'r%' and (pp1.gID in (" . implode(',', $groupIDs) . ") or pp1.uID = {$uID}))) > 0 or (p1.cPointerExternalLink !='' AND p1.cPointerExternalLink IS NOT NULL)");	
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
		$this->filterByCParentID = $cParentID;
		$this->filter('p1.cParentID', $cParentID);
	}
	
	/** 
	 * Filters by type of collection (using the ID field)
	 * @param mixed $ctID
	 */
	public function filterByCollectionTypeID($ctID) {
		$this->filterByCT = true;
		$this->filter("pt.ctID", $ctID);
	}

	/** 
	 * Filters by user ID of collection (using the uID field)
	 * @param mixed $ctID
	 */
	public function filterByUserID($uID) {
		$this->filter(false, "(p1.uID = $uID or p2.uID = $uID)");
	}

	public function filterByIsApproved($cvIsApproved) {
		$this->filter('cv.cvIsApproved', $cvIsApproved);	
	}
	
	public function filterByIsAlias($ia) {
		if ($ia == true) {
			$this->filter(false, "(p2.cPointerID is not null)");
		} else {
			$this->filter(false, "(p2.cPointerID is null)");
		}
	}
	
	/** 
	 * Filters by type of collection (using the handle field)
	 * @param mixed $ctID
	 */
	public function filterByCollectionTypeHandle($ctHandle) {
		$db = Loader::db();
		$this->filterByCT = true;
		if (is_array($ctHandle)) {
			$cth = '(';
			for ($i = 0; $i < count($ctHandle); $i++) {
				if ($i > 0) {
					$cth .= ',';
				}
				$cth .= $db->quote($ctHandle[$i]);
			}
			$cth .= ')';
			$this->filter(false, "(pt.ctHandle in {$cth})");
		} else {
			$this->filter('pt.ctHandle', $ctHandle);
		}
	}

	/** 
	 * Filters by date added
	 * @param string $date
	 */
	public function filterByDateAdded($date, $comparison = '=') {
		$this->filter('c.cDateAdded', $date, $comparison);
	}
	
	public function filterByNumberOfChildren($num, $comparison = '>') {
		if (!Loader::helper('validation/numbers')->integer($num)) {
			$num = 0;
		}
		$this->filter(false, '(p1.cChildren ' . $comparison . ' ' . $num . ' or p2.cChildren ' . $comparison . ' ' . $num . ')');
	}

	/** 
	 * Filters by public date
	 * @param string $date
	 */
	public function filterByPublicDate($date, $comparison = '=') {
		$this->filter('cv.cvDatePublic', $date, $comparison);
	}
	
	/** 
	 * If true, pages will be checked for permissions prior to being returned
	 * @param bool $checkForPermissions
	 */
	public function displayOnlyPermittedPages($checkForPermissions) {
		$this->displayOnlyPermittedPages = $checkForPermissions;
	}
	
	protected function setBaseQuery($additionalFields = '') {
		$this->setQuery('select p1.cID, pt.ctHandle ' . $additionalFields . ' from Pages p1 left join Pages p2 on (p1.cPointerID = p2.cID) left join PageTypes pt on (pt.ctID = (if (p2.cID is null, p1.ctID, p2.cID))) left join PagePaths on (PagePaths.cID = p1.cID and PagePaths.ppIsCanonical = 1) left join PageSearchIndex psi on (psi.cID = if(p2.cID is null, p1.cID, p2.cID)) inner join CollectionVersions cv on (cv.cID = if(p2.cID is null, p1.cID, p2.cID) and cvID = (select max(cvID) from CollectionVersions where cID = cv.cID)) inner join Collections c on (c.cID = if(p2.cID is null, p1.cID, p2.cID))');
	}
	
	protected function setupSystemPagesToExclude() {
		if ($this->filterByCParentID > 1 || $this->filterByCT == true) {
			return false;
		}
		$cIDs = Cache::get('page_list_exclude_ids', false);
		if ($cIDs == false) {
			$db = Loader::db();
			$filters = ''; 
			for ($i = 0; $i < count($this->systemPagesToExclude); $i++) {
				$spe = $this->systemPagesToExclude[$i];
				$filters .= 'cFilename like \'/' . $spe . '\' ';
				if ($i + 1 < count($this->systemPagesToExclude)) {
					$filters .= 'or ';
				}
			}
			$cIDs = $db->GetCol("select cID from Pages where 1=1 and ctID = 0 and (" . $filters . ")");
			if (count($cIDs) > 0) {
				Cache::set('page_list_exclude_ids', false, $cIDs);
			}
		}
		$cIDStr = implode(',', $cIDs);
		$this->filter(false, "(p1.cID not in ({$cIDStr}) or p2.cID not in ({$cIDStr}))");
	}
	
	protected function loadPageID($cID) {
		return Page::getByID($cID);
	}
	
	public function getTotal() {
		if ($this->getQuery() == '') {
			$this->setBaseQuery();
		}		
		return parent::getTotal();
	}
	
	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$pages = array();
		if ($this->getQuery() == '') {
			$this->setBaseQuery();
		}		
		if ($this->displayOnlyApprovedPages) {
			$this->filter('cvIsApproved', 1);
		}
		$this->filter(false, "(p1.cIsTemplate = 0 or p2.cIsTemplate = 0)");
		$this->setItemsPerPage($itemsToGet);
		$this->setupPermissions();
		$this->setupAttributeFilters("left join CollectionSearchIndexAttributes on (CollectionSearchIndexAttributes.cID = if (p2.cID is null, p1.cID, p2.cID))");
		$this->setupSystemPagesToExclude();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$nc = $this->loadPageID($row['cID']);
			if (!$this->displayOnlyApprovedPages) {
				$nc->loadVersionObject('RECENT');
			} else {
				$nc->loadVersionObject();
			}
			$pages[] = $nc;
		}
		return $pages;
	}
}
