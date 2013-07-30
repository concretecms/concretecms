<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* An object that allows a filtered list of pages to be returned.
* @package Pages
*
*/
class Concrete5_Model_PageList extends DatabaseItemList {

	protected $includeSystemPages = false;
	protected $attributeFilters = array();
	protected $includeAliases = true;
	protected $displayOnlyPermittedPages = false; // not used.
	protected $displayOnlyApprovedPages = true;
	protected $displayOnlyActivePages = true;
	protected $filterByCParentID = 0;
	protected $filterByCT = false;
	protected $ignorePermissions = false;
	protected $attributeClass = 'CollectionAttributeKey';
	protected $autoSortColumns = array('cvName', 'cvDatePublic', 'cDateAdded', 'cDateModified');
	protected $indexedSearch = false;
	protected $viewPagePermissionKeyHandle = 'view_page';
	
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
	
	public function setViewPagePermissionKeyHandle($pkHandle) {
		$this->viewPagePermissionKeyHandle = $pkHandle;
	}

	public function includeInactivePages() {
		$this->displayOnlyActivePages = false;
	}
	
	public function ignorePermissions() {
		$this->ignorePermissions = true;
	}
	
	public function ignoreAliases() {
		$this->includeAliases = false;
	}

	public function includeSystemPages() {
		$this->includeSystemPages = true;
	}
	
	public function displayUnapprovedPages() {
		$this->displayOnlyApprovedPages = false;
	}
	
	public function isIndexedSearch() {return $this->indexedSearch;}
	/** 
	 * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
	 */
	public function filterByKeywords($keywords, $simple = false) {
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

		if ($simple || $this->indexModeSimple) { // $this->indexModeSimple is set by the IndexedPageList class
			$this->filter(false, "(psi.cName like $qk or psi.cDescription like $qk or psi.content like $qk {$attribsStr})");		
		} else {
			$this->indexedSearch = true;
			$this->indexedKeywords = $keywords;
			$this->autoSortColumns[] = 'cIndexScore';
			$this->filter(false, "((match(psi.cName, psi.cDescription, psi.content) against ({$kw})) {$attribsStr})");
		}
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
		
		$accessEntities = $u->getUserAccessEntityObjects();
		foreach($accessEntities as $pae) {
			$peIDs[] = $pae->getAccessEntityID();
		}
		
		$owpae = PageOwnerPermissionAccessEntity::getOrCreate();
		// now we retrieve a list of permission duration object IDs that are attached view_page or view_page_version
		// against any of these access entity objects. We just get'em all.
		$db = Loader::db();
		$activePDIDs = array();
		$vpPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->viewPagePermissionKeyHandle));
		/*
		$vpvPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = \'view_page_versions\'');
		$pdIDs = $db->GetCol("select distinct pdID from PagePermissionAssignments ppa inner join PermissionAccessList pa on ppa.paID = pa.paID where pkID in (?, ?) and pdID > 0", array($vpPKID, $vpvPKID));
		*/
		$pdIDs = $db->GetCol("select distinct pdID from PagePermissionAssignments ppa inner join PermissionAccessList pa on ppa.paID = pa.paID where pkID =? and pdID > 0", array($vpPKID));
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

		if ($this->includeAliases) {
			$cInheritPermissionsFromCID = 'if(p2.cID is null, p1.cInheritPermissionsFromCID, p2.cInheritPermissionsFromCID)';
		} else {
			$cInheritPermissionsFromCID = 'p1.cInheritPermissionsFromCID';
		}

		if ($this->displayOnlyApprovedPages) {
			$cvIsApproved = ' and cv.cvIsApproved = 1';
		}
		
		$uID = 0;
		if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}
		
		/*
		$this->filter(false, "((select count(cID) from PagePermissionAssignments ppa1 inner join PermissionAccessList pa1 on ppa1.paID = pa1.paID where ppa1.cID = {$cInheritPermissionsFromCID} and pa1.accessType = " . PermissionKey::ACCESS_TYPE_INCLUDE . " and pa1.pdID in (" . implode(',', $activePDIDs) . ")
			and pa1.peID in (" . implode(',', $peIDs) . ") and (if(pa1.peID = " . $owpae->getAccessEntityID() . " and p1.uID <>" . $uID . ", false, true)) and (ppa1.pkID = " . $vpPKID . $cvIsApproved . " or ppa1.pkID = " . $vpvPKID . ")) > 0
			or (p1.cPointerExternalLink !='' AND p1.cPointerExternalLink IS NOT NULL))");
		$this->filter(false, "((select count(cID) from PagePermissionAssignments ppaExclude inner join PermissionAccessList paExclude on ppaExclude.paID = paExclude.paID where ppaExclude.cID = {$cInheritPermissionsFromCID} and accessType = " . PermissionKey::ACCESS_TYPE_EXCLUDE . " and pdID in (" . implode(',', $activePDIDs) . ")
			and paExclude.peID in (" . implode(',', $peIDs) . ") and (if(paExclude.peID = " . $owpae->getAccessEntityID() . " and p1.uID <>" . $uID . ", false, true)) and (ppaExclude.pkID = " . $vpPKID . $cvIsApproved . " or ppaExclude.pkID = " . $vpvPKID . ")) = 0)");		
			*/

		$this->filter(false, "((select count(cID) from PagePermissionAssignments ppa1 inner join PermissionAccessList pa1 on ppa1.paID = pa1.paID where ppa1.cID = {$cInheritPermissionsFromCID} and pa1.accessType = " . PermissionKey::ACCESS_TYPE_INCLUDE . " and pa1.pdID in (" . implode(',', $activePDIDs) . ")
			and pa1.peID in (" . implode(',', $peIDs) . ") and (if(pa1.peID = " . $owpae->getAccessEntityID() . " and p1.uID <>" . $uID . ", false, true)) and (ppa1.pkID = " . $vpPKID . $cvIsApproved . ")) > 0
			or (p1.cPointerExternalLink !='' AND p1.cPointerExternalLink IS NOT NULL))");
		$this->filter(false, "((select count(cID) from PagePermissionAssignments ppaExclude inner join PermissionAccessList paExclude on ppaExclude.paID = paExclude.paID where ppaExclude.cID = {$cInheritPermissionsFromCID} and accessType = " . PermissionKey::ACCESS_TYPE_EXCLUDE . " and pdID in (" . implode(',', $activePDIDs) . ")
			and paExclude.peID in (" . implode(',', $peIDs) . ") and (if(paExclude.peID = " . $owpae->getAccessEntityID() . " and p1.uID <>" . $uID . ", false, true)) and (ppaExclude.pkID = " . $vpPKID . $cvIsApproved . ")) = 0)");		

	}

	public function sortByRelevance() {
		if ($this->indexedSearch) {
			parent::sortBy('cIndexScore', 'desc');
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
		parent::sortBy('p1.cDisplayOrder', 'desc');
	}

	public function sortByCollectionIDAscending() {
		parent::sortBy('p1.cID', 'asc');
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
		$db = Loader::db();
		if (is_array($cParentID)) {
			$cth = '(';
			for ($i = 0; $i < count($cParentID); $i++) {
				if ($i > 0) {
					$cth .= ',';
				}
				$cth .= $db->quote($cParentID[$i]);
			}
			$cth .= ')';
			$this->filter(false, "(p1.cParentID in {$cth})");
		} else {
			$this->filterByCParentID = $cParentID;
			$this->filter('p1.cParentID', $cParentID);
		}
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
		if ($this->includeAliases) {
			$this->filter(false, "(p1.uID = $uID or p2.uID = $uID)");
		} else {
			$this->filter('p1.uID', $uID);
		}
	}

	public function filterByIsApproved($cvIsApproved) {
		$this->filter('cv.cvIsApproved', $cvIsApproved);	
	}
	
	public function filterByIsAlias($ia) {
		if ($this->includeAliases) {
			if ($ia == true) {
				$this->filter(false, "(p2.cPointerID is not null)");
			} else {
				$this->filter(false, "(p2.cPointerID is null)");
			}
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
		if ($this->includeAliases) {
			$this->filter(false, '(p1.cChildren ' . $comparison . ' ' . $num . ' or p2.cChildren ' . $comparison . ' ' . $num . ')');
		} else {
			$this->filter('p1.cChildren', $num, $comparison);
		}
	}

	public function filterByDateLastModified($date, $comparison = '=') {
		$this->filter('c.cDateModified', $date, $comparison);
	}

	/** 
	 * Filters by public date
	 * @param string $date
	 */
	public function filterByPublicDate($date, $comparison = '=') {
		$this->filter('cv.cvDatePublic', $date, $comparison);
	}
		
	/***
	 * Like filterByAttribute(), but wraps values properly for "select" type attributes.
	 * Accepts either a single value, or an array of values.
	 * If an array of values is provided, they will be combined together with "OR".
	 * (If you need to do an "AND" filter on mulitple values, just call this function multiple times).
	 */
	public function filterBySelectAttribute($akHandle, $value) {
		if (empty($value)) {
			return;
		}
		
		//Determine if this attribute allows multiple selections
		$ak = CollectionAttributeKey::getByHandle($akHandle);
		$akc = $ak->getController();
		$isMultiSelect = $akc->getAllowMultipleValues();
		
		//Explanation of query logic: "select" attributes wrap each value in newline characters when
		// saving to the db, which allows parsing of individual values within a "multi-select" attribute.
		//
		//Because of this, you need to query them using the "LIKE" operator and the "%" wildcards
		// when the attribute is "multi-select" (although for "single-select" attributes
		// you can speed things up by just using "=" and excluding the "%" wildcards).
		//
		//Things get trickier if you want to string together several values with an "OR"
		// (for example, "find all pages whose 'tags' attribute is 'hello' OR 'world'")
		// -- the usual "filterBy" methods don't work because they always use "AND" to combine
		// multiple criteria. So instead we can manually create our own portion of the "WHERE"
		// clause and pass that directly to the raw "filter" attribute.
		if (is_array($value)) {
			$db = Loader::db();
			$criteria = array();
			foreach ($value as $v) {
				$escapedValue = $db->escape($v);
				if ($isMultiSelect) {
					$criteria[] = "(ak_{$akHandle} LIKE '%\n{$escapedValue}\n%')";
				} else {
					$criteria[] = "(ak_{$akHandle} = '\n{$escapedValue}\n')";
				}
			}
			$where = '(' . implode(' OR ', $criteria) . ')';
			$this->filter(false, $where);
		} else if ($isMultiSelect) {
			$this->filterByAttribute($akHandle, "%\n{$value}\n%", 'LIKE');
		} else {
			$this->filterByAttribute($akHandle, "\n{$value}\n");
		}
	}
	
	/** 
	 * If true, pages will be checked for permissions prior to being returned
	 * @param bool $checkForPermissions
	 */
	public function displayOnlyPermittedPages($checkForPermissions) {
		if ($checkForPermissions) {
			$this->ignorePermissions = false;
		} else {
			$this->ignorePermissions = true;
		}
	}
	
	protected function setBaseQuery($additionalFields = '') {
		if ($this->isIndexedSearch()) {
			$db = Loader::db();
			$ik = ', match(psi.cName, psi.cDescription, psi.content) against (' . $db->quote($this->indexedKeywords) . ') as cIndexScore ';
		}
	
		if (!$this->includeAliases) {
			$this->filter(false, '(p1.cPointerID < 1 or p1.cPointerID is null)');
		}
		
		$cvID = '(select max(cvID) from CollectionVersions where cID = cv.cID)';		
		if ($this->displayOnlyApprovedPages) {
			$cvID = '(select cvID from CollectionVersions where cvIsApproved = 1 and cID = cv.cID)';
			$this->filter('cvIsApproved', 1);
		}

		if ($this->includeAliases) {
			$this->setQuery('select p1.cID, pt.ctHandle ' . $ik . $additionalFields . ' from Pages p1 left join Pages p2 on (p1.cPointerID = p2.cID) left join PagePaths on (PagePaths.cID = p1.cID and PagePaths.ppIsCanonical = 1) left join PageSearchIndex psi on (psi.cID = if(p2.cID is null, p1.cID, p2.cID)) inner join CollectionVersions cv on (cv.cID = if(p2.cID is null, p1.cID, p2.cID) and cvID = ' . $cvID . ') left join PageTypes pt on pt.ctID = cv.ctID inner join Collections c on (c.cID = if(p2.cID is null, p1.cID, p2.cID))');
		} else {
			$this->setQuery('select p1.cID, pt.ctHandle ' . $ik . $additionalFields . ' from Pages p1 left join PagePaths on (PagePaths.cID = p1.cID and PagePaths.ppIsCanonical = 1) left join PageSearchIndex psi on (psi.cID = p1.cID) inner join CollectionVersions cv on (cv.cID = p1.cID and cvID = ' . $cvID . ') left join PageTypes pt on (pt.ctID = cv.ctID)  inner join Collections c on (c.cID = p1.cID)');
		}
		
		if ($this->includeAliases) {
			$this->filter(false, "(p1.cIsTemplate = 0 or p2.cIsTemplate = 0)");
		} else {
			$this->filter('p1.cIsTemplate', 0);
		}
		
		$this->setupPermissions();
		
		if ($this->includeAliases) {
			$this->setupAttributeFilters("left join CollectionSearchIndexAttributes on (CollectionSearchIndexAttributes.cID = if (p2.cID is null, p1.cID, p2.cID))");
		} else {
			$this->setupAttributeFilters("left join CollectionSearchIndexAttributes on (CollectionSearchIndexAttributes.cID = p1.cID)");
		}
		
		if ($this->displayOnlyActivePages) {
			$this->filter('p1.cIsActive', 1);
		}
		$this->setupSystemPagesToExclude();
		
	}
	
	protected function setupSystemPagesToExclude() {
		if ($this->includeSystemPages || $this->filterByCParentID > 1 || $this->filterByCT == true) {
			return false;
		}
		if ($this->includeAliases) {
			$this->filter(false, "(p1.cIsSystemPage = 0 or p2.cIsSystemPage = 0)");	
		} else {
			$this->filter(false, "(p1.cIsSystemPage = 0)");	
		}
	}
	
	protected function loadPageID($cID, $versionOrig = 'RECENT') {
		return Page::getByID($cID, $versionOrig);
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
		
		$this->setItemsPerPage($itemsToGet);
		
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$nc = $this->loadPageID($row['cID'], 'ACTIVE');
			if (!$this->displayOnlyApprovedPages) {
				$cp = new Permissions($nc);
				if ($cp->canViewPageVersions()) {
					$nc->loadVersionObject('RECENT');
				}
			}
			$nc->setPageIndexScore($row['cIndexScore']);
			$pages[] = $nc;
		}
		return $pages;
	}
}

class Concrete5_Model_PageSearchDefaultColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'CollectionAttributeKey';	

	public static function getCollectionDatePublic($c) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($c->getCollectionDatePublic()));
	}

	public static function getCollectionDateModified($c) {
		return date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($c->getCollectionDateLastModified()));
	}
	
	public function getCollectionAuthor($c) {
		$uID = $c->getCollectionUserID();
		$ui = UserInfo::getByID($uID);
		if (is_object($ui)) {
			return $ui->getUserName();
		}
	}
	
	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('ctHandle', t('Type'), 'getCollectionTypeName', false));
		$this->addColumn(new DatabaseItemListColumn('cvName', t('Name'), 'getCollectionName'));
		$this->addColumn(new DatabaseItemListColumn('cvDatePublic', t('Date'), array('PageSearchDefaultColumnSet', 'getCollectionDatePublic')));
		$this->addColumn(new DatabaseItemListColumn('cDateModified', t('Last Modified'), array('PageSearchDefaultColumnSet', 'getCollectionDateModified')));
		$this->addColumn(new DatabaseItemListColumn('author', t('Author'), array('PageSearchDefaultColumnSet', 'getCollectionAuthor'), false));
		$date = $this->getColumnByKey('cDateModified');
		$this->setDefaultSortColumn($date, 'desc');
	}
}

class Concrete5_Model_PageSearchAvailableColumnSet extends Concrete5_Model_PageSearchDefaultColumnSet {
	protected $attributeClass = 'CollectionAttributeKey';
	public function __construct() {
		parent::__construct();
	}
}

class Concrete5_Model_PageSearchColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'CollectionAttributeKey';
	public function getCurrent() {
		$u = new User();
		$fldc = $u->config('PAGE_LIST_DEFAULT_COLUMNS');
		if ($fldc != '') {
			$fldc = @unserialize($fldc);
		}
		if (!($fldc instanceof DatabaseItemListColumnSet)) {
			$fldc = new PageSearchDefaultColumnSet();
		}
		return $fldc;
	}
}
