<?php 

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
	private $systemPagesToExclude = array('login.php', 'register.php', 'download_file.php', 'profile/%', 'dashboard/%');
	private $filterByCParentID = 0;
	private $ignorePermissions = false;
	protected $attributeClass = 'CollectionAttributeKey';
	
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
		
		$this->addToQuery('left join PagePermissions pp1 on (pp1.cID = p1.cInheritPermissionsFromCID) left join PagePermissions pp2 on (pp2.cID = p2.cInheritPermissionsFromCID)');
		$this->filter(false, "((pp1.cgPermissions like 'r%' and (pp1.gID in (" . implode(',', $groupIDs) . ") or pp1.uID = {$uID})) or (pp2.cgPermissions like 'r%' and (pp2.gID in (" . implode(',', $groupIDs) .  ") or pp2.uID = {$uID})) or (p1.cPointerExternalLink !='' AND p1.cPointerExternalLink IS NOT NULL ))");
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
		$this->filter(false, "(p1.ctID = $ctID or p2.ctID = $ctID)");
	}

	/** 
	 * Filters by user ID of collection (using the uID field)
	 * @param mixed $ctID
	 */
	public function filterByUserID($uID) {
		$this->filter(false, "(p1.uID = $uID or p2.uID = $uID)");
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
		if (is_array($ctHandle)) {
			$cth = '(';
			for ($i = 0; $i < count($ctHandle); $i++) {
				if ($i > 0) {
					$cth .= ',';
				}
				$cth .= $db->quote($ctHandle[$i]);
			}
			$cth .= ')';
			$this->filter(false, "(pt1.ctHandle in {$cth} or pt2.ctHandle in {$cth})");
		} else {
			$this->filter(false, "(pt1.ctHandle = " . $db->quote($ctHandle) . " or pt2.ctHandle = " . $db->quote($ctHandle) . ")");
		}
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
		$this->setQuery('select distinct p1.cID, if(p2.cID is null, pt1.ctHandle, pt2.ctHandle) as ctHandle ' . $additionalFields . ' from Pages p1 left join Pages p2 on (p1.cPointerID = p2.cID) left join PageTypes pt1 on (pt1.ctID = p1.ctID) left join PageTypes pt2 on (pt2.ctID = p2.ctID) inner join CollectionVersions cv on (cv.cID = if(p2.cID is null, p1.cID, p2.cID))');
	}
	
	protected function setupSystemPagesToExclude() {
		if ($this->filterByCParentID > 1) {
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
	
	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$pages = array();
		if ($this->getQuery() == '') {
			$this->setBaseQuery();
		}		
		$this->filter('cvIsApproved', 1);
		$this->filter(false, "(p1.cIsTemplate = 0 or p2.cIsTemplate = 0)");
		$this->setItemsPerPage($itemsToGet);
		$this->setupPermissions();
		$this->setupAttributeFilters("left join CollectionSearchIndexAttributes on (CollectionSearchIndexAttributes.cID = if (p2.cID is null, p1.cID, p2.cID))");
		$this->setupSystemPagesToExclude();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$nc = $this->loadPageID($row['cID']);
			$nc->loadVersionObject();
			$pages[] = $nc;
		}
		return $pages;
	}
}
