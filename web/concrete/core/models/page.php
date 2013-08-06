<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* The page object in Concrete encapsulates all the functionality used by a typical page and their contents
* including blocks, page metadata, page permissions. 
* @package Pages
*
*/
class Concrete5_Model_Page extends Collection {

	protected $blocksAliasedFromMasterCollection = null;
	
	/**
	 * @param string $path /path/to/page
	 * @param string $version ACTIVE or RECENT
	 * @return Page
	 */
	public static function getByPath($path, $version = 'RECENT') {
		$path = rtrim($path, '/'); // if the path ends in a / remove it.

		$cID = CacheLocal::getEntry('page_id_from_path', $path);
		if ($cID == false) {
			$db = Loader::db();
			$cID = $db->GetOne("select cID from PagePaths where cPath = ?", array($path));
			CacheLocal::set("page_id_from_path", $path, $cID);
		}
		return Page::getByID($cID, $version);
	}
	
	/**
	 * @param int $cID Collection ID of a page
	 * @param string $versionOrig ACTIVE or RECENT
	 * @param string $class
	 * @return Page
	 */
	public static function getByID($cID, $version = 'RECENT', $class = 'Page') {
		
		$c = CacheLocal::getEntry('page', $cID . ':' . $version);
		if ($c instanceof $class) {
			return $c;
		}
		
		$where = "where Pages.cID = ?";
		$c = new $class;
		$c->populatePage($cID, $where, $version);
 
		// must use cID instead of c->getCollectionID() because cID may be the pointer to another page		
		CacheLocal::set('page', $cID . ':' . $version, $c);
		
		return $c;
	}
	
	/**
	 * @access private
	 */
	protected function populatePage($cInfo, $where, $cvID) {
		$db = Loader::db();
		
		$q0 = "select Pages.cID, Pages.pkgID, Pages.cPointerID, Pages.cPointerExternalLink, Pages.cIsActive, Pages.cIsSystemPage, Pages.cPointerExternalLinkNewWindow, Pages.cFilename, Collections.cDateAdded, Pages.cDisplayOrder, Collections.cDateModified, cInheritPermissionsFromCID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cCheckedOutUID, cIsTemplate, uID, cPath, cParentID, cChildren, cCacheFullPageContent, cCacheFullPageContentOverrideLifetime, cCacheFullPageContentLifetimeCustom from Pages inner join Collections on Pages.cID = Collections.cID left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ";
		//$q2 = "select cParentID, cPointerID, cPath, Pages.cID from Pages left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ";
		
		$v = array($cInfo);
		$r = $db->query($q0 . $where, $v);
		$row = $r->fetchRow();
		if ($row['cPointerID'] > 0) {
			$q1 = $q0 . "where Pages.cID = ?";
			$cPointerOriginalID = $row['cID'];
			$v = array($row['cPointerID']);
			$cParentIDOverride = $row['cParentID'];
			$cPathOverride = $row['cPath'];
			$cPointerID = $row['cPointerID'];
			$cDisplayOrderOverride = $row['cDisplayOrder'];
			$r = $db->query($q1, $v);
			$row = $r->fetchRow();
		}
	
		if ($r) {
			if ($row) {
				foreach ($row as $key => $value) {
					$this->{$key} = $value;
				}
				if (isset($cParentIDOverride)) {
					$this->cPointerID = $cPointerID;
					$this->cPointerOriginalID = $cPointerOriginalID;
					$this->cPath = $cPathOverride;
					$this->cParentID = $cParentIDOverride;
				}
				$this->isMasterCollection = $row['cIsTemplate'];
			} else {
				if ($cInfo == 1) {
					$this->cID = '1';
					$this->loadError(COLLECTION_INIT);
				} else {
					// there was no record of this particular collection in the database
					$this->loadError(COLLECTION_NOT_FOUND);
				}
			}
			$r->free();
		} else {
			$this->loadError(COLLECTION_NOT_FOUND);
		}
		
		if ($cvID != false && !$this->isError()) {
			$this->loadVersionObject($cvID);
		}
		
		unset($r);
	}	
	

	public function getPermissionObjectIdentifier() {
		// this is a hack but it's a really good one for performance
		// if the permission access entity for page owner exists in the database, then we return the collection ID. Otherwise, we just return the permission collection id
		// this is because page owner is the ONLY thing that makes it so we can't use getPermissionsCollectionID, and for most sites that will DRAMATICALLY reduce the number of queries.
		if (PAGE_PERMISSION_IDENTIFIER_USE_PERMISSION_COLLECTION_ID) {
			return $this->getPermissionsCollectionID();
		} else {
			return $this->getCollectionID();
		}
	}

	/**
	 * Returns 1 if the page is in edit mode
	 * @return bool
	 */
	public function isEditMode() {
		$v = View::getInstance();
		return ($this->isCheckedOutByMe() && ($v->editingEnabled()));
	}
	
	/**
	 * Get the package ID for a page (page thats added by a package) (returns 0 if its not in a package)
	 * @return int
	 */
	public function getPackageID() {return $this->pkgID;}
	
	/**
	 * Get the package handle for a page (page thats added by a package)
	 * @return string
	 */
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	/**
	 * Returns 1 if the page is in arrange mode
	 * @return bool
	 */
	public function isArrangeMode() {return ($this->isCheckedOutByMe() && ($_REQUEST['btask'] == 'arrange'));}
	
	/**
	 * Forces the page to be checked in if its checked out
	 */
	public function forceCheckIn() {
		// This function forces checkin to take place
		$db = Loader::db();
		$q = "update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cID = '{$this->cID}'";
		$r = $db->query($q);
	}

	/**
	 * Checks if the page is a dashboard page, returns true if it is
	 * @return bool
	 */	
	public function isAdminArea() {
		if ($this->isGeneratedCollection()) {
			$pos = strpos($this->getCollectionFilename(), "/" . DIRNAME_DASHBOARD);
			return ($pos > -1);
		}			
		return false;
	}
	
	/** 
	 * Takes an array of area/block values and makes that the arrangement for this page's version
	 * Format is like: $area[10][0] = 2, $area[10][1] = 8, $area[15][0] = 27, with the area ID being the 
	 * key and the block IDs being 1-n values inside it
	 * @param array $areas
	 */
	public function processArrangement($areas) {

		// this function is called via ajax, so it's a bit wonky, but the format is generally
		// a{areaID} = array(b1, b2, b3) (where b1, etc... are blocks with ids appended.)
		$db = Loader::db();		
		$db->Execute('delete from CollectionVersionBlockStyles where cID = ? and cvID = ?', array($this->getCollectionID(), $this->getVersionID()));		
		foreach($areas as $arID => $blocks) {
			if (intval($arID) > 0) {
				// this is a serialized area;
				$arHandle = $db->getOne("select arHandle from Areas where arID = ?", array($arID));
				$startDO = 0;
				
				foreach($blocks as $bIdentifier) {

					$bID = 0;
					$csrID = 0;
					
					$bd2 = explode('-', $bIdentifier);
					$bID = $bd2[0];
					$csrID = $bd2[1];

					if (intval($bID) > 0) {
						$v = array($startDO, $arHandle, $bID, $this->getCollectionID(), $this->getVersionID());
						try {
							$db->query("update CollectionVersionBlocks set cbDisplayOrder = ?, arHandle = ? where bID = ? and cID = ? and (cvID = ? or cbIncludeAll = 1)", $v);
							if ($csrID > 0) {
								$db->query("insert into CollectionVersionBlockStyles (csrID, arHandle, bID, cID, cvID) values (?, ?, ?, ?, ?)", array(
									$csrID, $arHandle, $bID, $this->getCollectionID(), $this->getVersionID()
								));
							}
							// update the style for any of these blocks
							
						} catch(Exception $e) {}
						
						$startDO++;
					}
				}
			}
		}
	}


	/**
	 * checks if the page is checked out, if it is return true
	 * @return bool
	 */	
	function isCheckedOut() {
		// function to inform us as to whether the current collection is checked out
		$db = Loader::db();
		if (isset($this->isCheckedOutCache)) {
			return $this->isCheckedOutCache;
		}
		
		$dh = Loader::helper('date');
				
		$q = "select cIsCheckedOut, UNIX_TIMESTAMP('" . $dh->getSystemDateTime() . "') - UNIX_TIMESTAMP(cCheckedOutDatetimeLastEdit) as timeout from Pages where cID = '{$this->cID}'";
		$r = $db->query($q);
		if ($r) {
			$row = $r->fetchRow();
			if ($row['cIsCheckedOut'] == 0) {
				return false;
			} else {
				if ($row['timeout'] > CHECKOUT_TIMEOUT) {
					$this->forceCheckIn();
					$this->isCheckedOutCache = false;
					return false;
				} else {
					$this->isCheckedOutCache = true;
					return true;
				}
			}
		}
	}
	/** 
	* Gets the user that is editing the current page. 
	* $return string $name
	*/
	public function getCollectionCheckedOutUserName() {
		$db = Loader::db();
		$query = "select cCheckedOutUID from Pages where cID = ?";
		$vals=array($this->cID);
		$checkedOutId = $db->getOne($query, $vals);
		if(is_object(UserInfo::getByID($checkedOutId))){
		    $ui = UserInfo::getByID($checkedOutId);
		    $name=$ui->getUserName();
		}else{
		    $name= t('Unknown User');
		}
		return $name;
	}
	
	/**
	 * Checks if the page is checked out by the current user
	 * @return bool
	 */
	function isCheckedOutByMe() {
		$u = new User();
		return ($this->getCollectionCheckedOutUserID() > 0 && $this->getCollectionCheckedOutUserID() == $u->getUserID());
	}

	/**
	 * Checks if the page is a single page
	 * @return bool
	 */
	function isGeneratedCollection() {
		// generated collections are collections without types, that have special cFilename attributes
		return $this->cFilename != null && $this->vObj->ctID == 0;
	}
	
	public function assignPermissions($userOrGroup, $permissions = array(), $accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		if ($this->cInheritPermissionsFrom != 'OVERRIDE') { 
			$this->setPermissionsToManualOverride();
			$this->clearPagePermissions();
		}
		
		if (is_array($userOrGroup)) { 
			$pe = GroupCombinationPermissionAccessEntity::getOrCreate($userOrGroup);
			// group combination
		} else if ($userOrGroup instanceof User || $userOrGroup instanceof UserInfo) { 
			$pe = UserPermissionAccessEntity::getOrCreate($userOrGroup);
		} else { 
			// group;
			$pe = GroupPermissionAccessEntity::getOrCreate($userOrGroup);
		}

		foreach($permissions as $pkHandle) { 
			$pk = PagePermissionKey::getByHandle($pkHandle);
			$pk->setPermissionObject($this);
			$pa = $pk->getPermissionAccessObject();
			if (!is_object($pa)) {
				$pa = PermissionAccess::create($pk);
			} else if ($pa->isPermissionAccessInUse()) {
				$pa = $pa->duplicate();
			}
			$pa->addListItem($pe, false, $accessType);
			$pt = $pk->getPermissionAssignmentObject();
			$pt->assignPermissionAccess($pa);
		}
		
	}
	
	
	private static function translatePermissionsXMLToKeys($node) {
		$pkHandles = array();
		if ($node['canRead'] == '1') {
			$pkHandles[] = 'view_page';
			$pkHandles[] = 'view_page_in_sitemap';
		}
		if ($node['canWrite'] == '1') {
			$pkHandles[] = 'view_page_versions';
			$pkHandles[] = 'edit_page_properties';
			$pkHandles[] = 'edit_page_contents';
			$pkHandles[] = 'approve_page_versions';
			$pkHandles[] = 'move_or_copy_page';
			$pkHandles[] = 'preview_page_as_user';
			$pkHandles[] = 'add_subpage';
		}
		if ($node['canAdmin'] == '1') {
			$pkHandles[] = 'edit_page_speed_settings';
			$pkHandles[] = 'edit_page_permissions';
			$pkHandles[] = 'edit_page_theme';
			$pkHandles[] = 'schedule_page_contents_guest_access';
			$pkHandles[] = 'edit_page_type';
			$pkHandles[] = 'delete_page';
			$pkHandles[] = 'delete_page_versions';
		}
		return $pkHandles;
	}
	
	/** 
	 * @private
	 */
	public function assignPermissionSet($px) {
		// this is the legacy function that is called just by xml. We pass these values in as though they were the old ones.
		if (isset($px->guests)) {
			$pkHandles = self::translatePermissionsXMLToKeys($px->guests);
			$this->assignPermissions(Group::getByID(GUEST_GROUP_ID), $pkHandles);
		}	
		if (isset($px->registered)) {
			$pkHandles = self::translatePermissionsXMLToKeys($px->registered);
			$this->assignPermissions(Group::getByID(REGISTERED_GROUP_ID), $pkHandles);
		}	
		if (isset($px->administrators)) {
			$pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
			$this->assignPermissions(Group::getByID(ADMIN_GROUP_ID), $pkHandles);
		}
		if (isset($px->group)) {
			foreach($px->group as $g) {
				$pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
				$this->assignPermissions(Group::getByID($g['gID']), $pkHandles);
			}
		}	
		if (isset($px->user)) {
			foreach($px->user as $u) {
				$pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
				$this->assignPermissions(UserInfo::getByID($u['uID']), $pkHandles);
			}
		}
	}
	
	
	/**
	 * Make an alias to a page
	 * @param Collection $c
	 * @return int $newCID
	 */
	function addCollectionAlias($c) {
		$db = Loader::db();
		// the passed collection is the parent collection
		$cParentID = $c->getCollectionID();

		$u = new User();
		$uID = $u->getUserID();
		$ctID = 0;
		
		$dh = Loader::helper('date');
				
		$cDate = $dh->getSystemDateTime();
		$cDatePublic = $dh->getSystemDateTime();
		$handle = $this->getCollectionHandle();

		$_cParentID = $c->getCollectionID();
		$q = "select PagePaths.cPath from PagePaths where cID = '{$_cParentID}'";
		if ($_cParentID > 1) {
			$q .=  " and ppIsCanonical = 1";
		}
		$cPath = $db->getOne($q);
		
		$data['handle'] = $this->getCollectionHandle();
		$data['name'] = $this->getCollectionName();
		
		$cobj = parent::add($data);
		$newCID = $cobj->getCollectionID();
		
		$v = array($newCID, $cParentID, $uID, $this->getCollectionID());
		$q = "insert into Pages (cID, cParentID, uID, cPointerID) values (?, ?, ?, ?)";
		$r = $db->prepare($q);
		
		$res = $db->execute($r, $v);
		$newCID = $db->Insert_ID();

		Loader::model('page_statistics');		
		PageStatistics::incrementParents($newCID);

		
		$q2 = "insert into PagePaths (cID, cPath) values (?, ?)";
		$v2 = array($newCID, $cPath . '/' . $handle);
		$db->query($q2, $v2);
		

		return $newCID;
	}

	/**
	 * Update the name, link, and to open in a new window for an external link
	 * @param string $cName
	 * @param string $cLink
	 * @param bool $newWindow
	 */	
	function updateCollectionAliasExternal($cName, $cLink, $newWindow = 0) {
		if ($this->cPointerExternalLink != '') {
			$db = Loader::db();
			$this->markModified();
			if ($newWindow) {
				$newWindow = 1;
			} else {
				$newWindow = 0;
			}
			$db->query("update CollectionVersions set cvName = ? where cID = ?", array($cName, $this->cID));
			$db->query("update Pages set cPointerExternalLink = ?, cPointerExternalLinkNewWindow = ? where cID = ?", array($cLink, $newWindow, $this->cID));
		}
	}

	/**
	 * Add a new external link
	 * @param string $cName
	 * @param string $cLink
	 * @param bool $newWindow
	 * @return int $newCID
	 */	
	function addCollectionAliasExternal($cName, $cLink, $newWindow = 0) {

		$db = Loader::db();
		$dh = Loader::helper('date');
		$dt = Loader::helper('text');
		$u = new User();

		$cParentID = $this->getCollectionID();
		$uID = $u->getUserID();
				
		$cDate = $dh->getSystemDateTime();
		$cDatePublic = $dh->getSystemDateTime();
		$handle = $this->getCollectionHandle();
		
		// make the handle out of the title
		$handle = $dt->urlify($cLink);
		$data['handle'] = $handle;
		$data['name'] = $cName;
		
		$cobj = parent::add($data);
		$newCID = $cobj->getCollectionID();
		
		if ($newWindow) {
			$newWindow = 1;
		} else {
			$newWindow = 0;
		}
		
		$v = array($newCID, $cParentID, $uID, $cLink, $newWindow);
		$q = "insert into Pages (cID, cParentID, uID, cPointerExternalLink, cPointerExternalLinkNewWindow) values (?, ?, ?, ?, ?)";
		$r = $db->prepare($q);
		
		$res = $db->execute($r, $v);
		$newCID = $db->Insert_ID();

		Loader::model('page_statistics');		
		PageStatistics::incrementParents($newCID);

		Page::getByID($newCID)->movePageDisplayOrderToBottom();
		return $newCID;

	}

	/**
	 * Check if a page is a single page that is in the core (/concrete directory)
	 * @return bool
	 */		
	public function isSystemPage() {
		return $this->cIsSystemPage;
	}

	/**
	 * Gets the icon for a page (also fires the on_page_get_icon event)
	 * @return string $icon Path to the icon
	 */		
	public function getCollectionIcon() {
		// returns a fully qualified image link for this page's icon, either based on its collection type or if icon.png appears in its view directory
		$icon = '';
		
		$icon = Events::fire('on_page_get_icon', $this);
		
		if ($icon) {
			return $icon;
		}
		
		if ($this->isGeneratedCollection()) {
			if ($this->getPackageID() > 0) {
				if (is_dir(DIR_PACKAGES . '/' . $this->getPackageHandle())) {
					$dirp = DIR_PACKAGES;
					$url = BASE_URL . DIR_REL;
				} else {
					$dirp = DIR_PACKAGES_CORE;
					$url = ASSETS_URL;
				}
				$file = $dirp . '/' . $this->getPackageHandle() . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
				if (file_exists($file)) {
					$icon = $url . '/' . DIRNAME_PACKAGES . '/' . $this->getPackageHandle() . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
				}
			} else if (file_exists(DIR_FILES_CONTENT . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON)) {
				$icon = BASE_URL . DIR_REL . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
			} else if (file_exists(DIR_FILES_CONTENT_REQUIRED . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON)) {
				$icon = ASSETS_URL . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
			}
	
		} else {

		}
		return $icon;
	}

	/**
	 * Remove an external link/alias
	 * @return int $cIDRedir cID for the original page if the page was an alias
	 */		
	function removeThisAlias() {
		$cIDRedir = $this->getCollectionPointerID();
		$cPointerExternalLink = $this->getCollectionPointerExternalLink();
		
		if ($cPointerExternalLink != '') {
			$this->delete();
		} else if ($cIDRedir > 0) {
			$db = Loader::db();

			Loader::model('page_statistics');		
			PageStatistics::decrementParents($this->getCollectionPointerOriginalID());

			$args = array($this->getCollectionPointerOriginalID());
			$q = "delete from Pages where cID = ?";
			$r = $db->query($q, $args);

			$q = "delete from Collections where cID = ?";
			$r = $db->query($q, $args);

			$q = "delete from CollectionVersions where cID = ?";
			$r = $db->query($q, $args);
			
			$q = "delete from PagePaths where cID = ?";
			$r = $db->query($q, $args);

			return $cIDRedir;
		}
	}

	public function populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage = true) {
		$db = Loader::db();
		$children = $db->GetAll('select cID, cDisplayOrder from Pages where cParentID = ? order by cDisplayOrder asc', array($pageRow['cID']));
		if ($includeThisPage) {	
			$pages[] = array(
				'cID' => $pageRow['cID'],
				'cDisplayOrder' => $pageRow['cDisplayOrder'],
				'cParentID' => $cParentID,
				'level' => $level,
				'total' => count($children)
			);
		}
		$level++;
		$cParentID = $pageRow['cID'];
		if (count($children) > 0) {
			foreach($children as $pageRow) {
				$pages = $this->populateRecursivePages($pages, $pageRow, $cParentID, $level);
			}
		}
		return $pages;
	}

	public function queueForDeletionSort($a, $b) {
		if ($a['level'] > $b['level']) {
			return -1;
		}
		if ($a['level'] < $b['level']) {
			return 1;
		}
		return 0;
	}

	public function queueForDuplicationSort($a, $b) {
		if ($a['level'] > $b['level']) {
			return 1;
		}
		if ($a['level'] < $b['level']) {
			return -1;
		}
		if ($a['cDisplayOrder'] > $b['cDisplayOrder']) {
			return 1;
		}
		if ($a['cDisplayOrder'] < $b['cDisplayOrder']) {
			return -1;
		}
		if ($a['cID'] > $b['cID']) {
			return 1;
		}
		if ($a['cID'] < $b['cID']) {
			return -1;
		}		
		return 0;
	}

	public function queueForDeletion() {
		$pages = array();
		$includeThisPage = true;
		if ($this->getCollectionPath() == TRASH_PAGE_PATH) {
			// we're in the trash. we can't delete the trash. we're skipping over the trash node.
			$includeThisPage = false;
		}
		$pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, $includeThisPage);
		// now, since this is deletion, we want to order the pages by level, which
		// should get us no funny business if the queue dies.
		usort($pages, array('Page', 'queueForDeletionSort'));
		$q = Queue::get('delete_page');
		foreach($pages as $page) {
			$q->send(serialize($page));
		}
	}

	public function queueForDeletionRequest() {
		$pages = array();
		$includeThisPage = true;
		$pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, $includeThisPage);
		// now, since this is deletion, we want to order the pages by level, which
		// should get us no funny business if the queue dies.
		usort($pages, array('Page', 'queueForDeletionSort'));
		$q = Queue::get('delete_page_request');
		foreach($pages as $page) {
			$q->send(serialize($page));
		}
	}

	public function queueForDuplication($destination, $includeParent = true) {
		$pages = array();
		$pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, $includeParent);
		// now, since this is deletion, we want to order the pages by level, which
		// should get us no funny business if the queue dies.
		usort($pages, array('Page', 'queueForDuplicationSort'));
		$q = Queue::get('copy_page');
		foreach($pages as $page) {
			$page['destination'] = $destination->getCollectionID();
			$q->send(serialize($page));
		}
	}

	public function export($pageNode) {
		$p = $pageNode->addChild('page');
		$p->addAttribute('name', Loader::helper('text')->entities($this->getCollectionName()));
		$p->addAttribute('path', $this->getCollectionPath());
		$p->addAttribute('filename', $this->getCollectionFilename());
		$p->addAttribute('pagetype', $this->getCollectionTypeHandle());
		$p->addAttribute('description', Loader::helper('text')->entities($this->getCollectionDescription()));
		$p->addAttribute('package', $this->getPackageHandle());
		if ($this->getCollectionParentID() == 0 && $this->isSystemPage()) {
			$p->addAttribute('root', 'true');
		}

		$attribs = $this->getSetCollectionAttributes();
		if (count($attribs) > 0) {
			$attributes = $p->addChild('attributes');
			foreach($attribs as $ak) {
				$av = $this->getAttributeValueObject($ak);
				$cnt = $ak->getController();
				$cnt->setAttributeValue($av);
		 		$akx = $attributes->addChild('attributekey');
		 		$akx->addAttribute('handle', $ak->getAttributeKeyHandle());
				$cnt->exportValue($akx);
			}
		}

		$db = Loader::db();
		$r = $db->Execute('select arHandle from Areas where cID = ? and arIsGlobal = 0', array($this->getCollectionID()));
		while ($row = $r->FetchRow()) {
			$ax = Area::get($this, $row['arHandle']);
			$ax->export($p, $this);
		}
	}

	/**
	 * Returns the uID for a page that is checked out
	 * @return int
	 */	
	function getCollectionCheckedOutUserID() {
		return $this->cCheckedOutUID;
	}

	/**
	 * Returns the path for the current page
	 * @return string
	 */	
	function getCollectionPath() {
		return $this->cPath;
	}
	
	/**
	 * Returns the path for a page from its cID
	 * @param int cID
	 * @return string $path
	 */	
	public static function getCollectionPathFromID($cID) {
		$db = Loader::db();
		$path = $db->GetOne("select cPath from PagePaths inner join CollectionVersions on (PagePaths.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) where PagePaths.cID = ?", array($cID));
		return $path;
	}

	/**
	 * Returns the uID for a page ownder
	 * @return int
	 */	
	function getCollectionUserID() {
		return $this->uID;
	}

	/**
	 * Returns the page's handle
	 * @return string
	 */	
	function getCollectionHandle() {
		return $this->vObj->cvHandle;
	}

	/**
	 * Returns the page's name
	 * @return string
	 */	
	function getCollectionTypeName() {
		return $this->vObj->ctName;
	}

	/**
	 * Returns the Collection Type ID
	 * @return int
	 */	
	function getCollectionTypeID() {
		return $this->vObj->ctID;
	}

	/**
	 * Returns the Collection Type handle
	 * @return string
	 */	
	function getCollectionTypeHandle() {
		return $this->vObj->ctHandle;
	}

	/**
	 * Returns theme id for the collection
	 * @return int
	 */	
	function getCollectionThemeID() {
		if ($this->vObj->ptID < 1 && $this->cID != HOME_CID) {
			$c = Page::getByID(HOME_CID);
			return $c->getCollectionThemeID();
		} else {
			return $this->vObj->ptID;
		}	
	}
	
	/**
	 * Check if a block is an alias from a page default
	 * @param array $b
	 * @return bool
	 */	
	function isBlockAliasedFromMasterCollection(&$b) {
		//Retrieve info for all of this page's blocks at once (and "cache" it)
		// so we don't have to query the database separately for every block on the page.
		if (is_null($this->blocksAliasedFromMasterCollection)) {
			$db = Loader::db();
			$q = 'SELECT bID FROM CollectionVersionBlocks WHERE cID = ? AND isOriginal = 0 AND cvID = ? AND bID IN (SELECT bID FROM CollectionVersionBlocks AS cvb2 WHERE cvb2.cid = ?)';
			$v = array($this->getCollectionID(), $this->getVersionObject()->getVersionID(), $this->getMasterCollectionID());
			$r = $db->execute($q, $v);
			$this->blocksAliasedFromMasterCollection = $db->GetCol($q, $v);
		}
		
		return ($b->isAlias() && in_array($b->getBlockID(), $this->blocksAliasedFromMasterCollection));
	}

	/**
	 * Returns Collection's theme object
	 * @return PageTheme
	 */		
	function getCollectionThemeObject() {
		if ($this->vObj->ptID < 1) {
			return PageTheme::getSiteTheme();
		} else {
			$pl = PageTheme::getByID($this->vObj->ptID);
			return $pl;
		}		
	}

	/**
	 * Returns the page's name
	 * @return string
	 */		
	function getCollectionName() {
		if (isset($this->vObj)) {
			return $this->vObj->cvName;
		}
		return $this->cvName;
	}

	/**
	 * Returns the collection ID for the aliased page (returns 0 unless used on an actual alias)
	 * @return int
	 */	
	function getCollectionPointerID() {
		return $this->cPointerID;
	}

	/**
	 * Returns link for the aliased page
	 * @return string
	 */	
	function getCollectionPointerExternalLink() {
		return $this->cPointerExternalLink;
	}

	/**
	 * Returns if the alias opens in a new window
	 * @return bool
	 */	
	function openCollectionPointerExternalLinkInNewWindow() {
		return $this->cPointerExternalLinkNewWindow;
	}

	/**
	 * Checks to see if the page is an alias
	 * @return bool
	 */		
	function isAlias() {
		return $this->cPointerID > 0 || $this->cPointerExternalLink != null;
	}

	/**
	 * Checks if a page is an external link
	 * @return bool
	 */		
	function isExternalLink() {
		return ($this->cPointerExternalLink != null);
	}

	/**
	 * Get the original cID of a page
	 * @return int
	 */		
	function getCollectionPointerOriginalID()  {
		return $this->cPointerOriginalID;
	}

	/**
	 * Get the file name of a page (single pages)
	 * @return string
	 */	
	function getCollectionFilename() {
		return $this->cFilename;
	}
	
	/**
	 * Gets the date a the current version was made public, 
	 * if user is specified, returns in the current user's timezone
	 * @param string $dateFormat
	 * @param string $type (system || user)
	 * @return string date formated like: 2009-01-01 00:00:00 
	*/
	function getCollectionDatePublic($dateFormat = null, $type='system') {
		if(!$dateFormat) {
			$dateFormat = 'Y-m-d H:i:s';
		}
		$dh = Loader::helper('date');
		if(ENABLE_USER_TIMEZONES && $type == 'user') {
			$dh = Loader::helper('date');
			return $dh->getLocalDateTime($this->vObj->cvDatePublic, $dateFormat);
		} else {
			return $dh->date($dateFormat, strtotime($this->vObj->cvDatePublic));
		}
	}

	/**
	 * Get the description of a page
	 * @return string
	 */	
	function getCollectionDescription() {
		return $this->vObj->cvDescription;
	}

	/**
	 * Gets the cID of the page's parent
	 * @return int
	 */	
	function getCollectionParentID() {
		return $this->cParentID;
	}

	/**
	 * Get the Parent cID from a page by using a cID
	 * @param int $cID
	 * @return int
	 */		
	function getCollectionParentIDFromChildID($cID) {
		$db = Loader::db();
		$q = "select cParentID from Pages where cID = ?";
		$cParentID = $db->GetOne($q, array($cID));
		return $cParentID;
	}

	/**
	 * Returns an array of this cParentID and aliased parentIDs
	 * @return array $cID
	 */		
	function getCollectionParentIDs(){
		$cIDs=array($this->cParentID);
		$db = Loader::db(); 
		$aliasedParents=$db->getAll('SELECT cParentID FROM Pages WHERE cPointerID='.intval($this->cID).' ');
		foreach($aliasedParents as $aliasedParent)
			$cIDs[]=$aliasedParent['cParentID'];
		return $cIDs;
	}

	/**
	 * Checks if a page is a page default
	 * @return bool
	 */	
	function isMasterCollection() {
		return $this->isMasterCollection;
	}


	/**
	 * Gets the template permissions
	 * @return string
	 */	
	function overrideTemplatePermissions() {
		return $this->cOverrideTemplatePermissions;
	}

	/**
	 * Gets the position of the page in the sitemap
	 * @return int
	 */		
	function getCollectionDisplayOrder() {
		return $this->cDisplayOrder;
	}

	/**
	 * Set the theme for a page using the page object
	 * @param PageTheme $pl
	 */		
	public function setTheme($pl) {
		$db = Loader::db();
		$db->query('update CollectionVersions set ptID = ? where cID = ? and cvID = ?', array($pl->getThemeID(), $this->cID, $this->vObj->getVersionID()));
	}

	/**
	 * Set the permissions of sub-collections added beneath this permissions to inherit from the template
	 */		
	function setPermissionsInheritanceToTemplate() {
		$db = Loader::db();
		if ($this->cID) {
			$db->query("update Pages set cOverrideTemplatePermissions = 0 where cID = {$this->cID}");
		}
	}

	/**
	 * Set the permissions of sub-collections added beneath this permissions to inherit from the parent
	 */		
	function setPermissionsInheritanceToOverride() {
		$db = Loader::db();
		if ($this->cID) {
			$db->query("update Pages set cOverrideTemplatePermissions = 1 where cID = {$this->cID}");
		}
	}

	function getPermissionsCollectionID() {
		return $this->cInheritPermissionsFromCID;
	}

	function getCollectionInheritance() {
		return $this->cInheritPermissionsFrom;
	}

	function getParentPermissionsCollectionID() {
		$db = Loader::db();
		$v = array($this->cParentID);
		$q = "select cInheritPermissionsFromCID from Pages where cID = ?";
		$ppID = $db->getOne($q, $v);
		return $ppID;
	}

	function getPermissionsCollectionObject() {
		return Page::getByID($this->cInheritPermissionsFromCID, "RECENT");
	}
	
	function getMasterCollectionID() {
		$db = Loader::db();
		$q = "select p.cID from Pages p inner join CollectionVersions on p.cID = CollectionVersions.cID where CollectionVersions.ctID = '{$this->vObj->ctID}' and cIsTemplate = 1";
		$cID = $db->getOne($q);
		if ($cID) {
			return $cID;
		}
	}

	function getOriginalCollectionID() {
		// this is a bit weird...basically, when editing a master collection, we store the
		// master collection ID in session, along with the collection ID we were looking at before
		// moving to the master collection. This allows us to get back to that original collection
		return $_SESSION['ocID'];
	}

	function getNumChildren() {
		return $this->cChildren;
	}
	
	function getNumChildrenDirect() {
		// direct children only
		$db = Loader::db();
		$v = array($this->cID);
		$num = $db->getOne('select count(cID) as total from Pages where cParentID = ?', $v);
		if ($num) {
			return $num;
		}
		return 0;
	}
	
	/** 
	 * Returns the first child of the current page, or null if there is no child
	 * @param string $sortColumn
	 * @return Page
	 */
	public function getFirstChild($sortColumn = 'cDisplayOrder asc', $excludeSystemPages = false) {
		if ($excludeSystemPages) {
			$systemPages = ' and cIsSystemPage = 0';
		} else {
			$systemPages = '';
		}
		
		$db = Loader::db();
		$cID = $db->GetOne("select Pages.cID from Pages inner join CollectionVersions on Pages.cID = CollectionVersions.cID where cvIsApproved = 1 and cParentID = ? " . $systemPages . " order by {$sortColumn}", array($this->cID));
		if ($cID > 1) {
			return Page::getByID($cID, "ACTIVE");
		}
		return false;
	}

	function getCollectionChildrenArray( $oneLevelOnly=0 ) {
		$this->childrenCIDArray = array();
		$this->_getNumChildren($this->cID,$oneLevelOnly);
		return $this->childrenCIDArray;
	}

	function _getNumChildren($cID,$oneLevelOnly=0, $sortColumn = 'cDisplayOrder asc') {
		$db = Loader::db();
		$q = "select cID from Pages where cParentID = {$cID} and cIsTemplate = 0 order by {$sortColumn}";
		$r = $db->query($q);
		if ($r) {
			while ($row = $r->fetchRow()) {
				if ($row['cID'] > 0) {
					$this->childrenCIDArray[] = $row['cID'];
					if( !$oneLevelOnly ) $this->_getNumChildren($row['cID']);
				}
			}
		}
	}

	function canMoveCopyTo($cobj) {
		// ensures that we're not moving or copying to a collection inside our part of the tree
		$children = $this->getCollectionChildrenArray();
		$children[] = $this->getCollectionID();
		return (!in_array($cobj->getCollectionID(), $children));
	}

	function update($data) {
		$db = Loader::db();

		$vo = $this->getVersionObject();
		$cvID = $vo->getVersionID();
		$this->markModified();
		
		$cName = $this->getCollectionName();
		$cDescription = $this->getCollectionDescription();
		$cDatePublic = $this->getCollectionDatePublic();
		$ctID = $this->getCollectionTypeID();
		$uID = $this->getCollectionUserID();
		$pkgID = $this->getPackageID();
		$cFilename = $this->getCollectionFilename();
		
		$rescanTemplatePermissions = false;
		
		$cCacheFullPageContent = $this->cCacheFullPageContent;
		$cCacheFullPageContentLifetimeCustom = $this->cCacheFullPageContentLifetimeCustom;
		$cCacheFullPageContentOverrideLifetime = $this->cCacheFullPageContentOverrideLifetime;
		
		if (isset($data['cName'])) {
			$cName = $data['cName'];
		}
		if (isset($data['cCacheFullPageContent'])) {
			$cCacheFullPageContent = $data['cCacheFullPageContent'];
		}
		if (isset($data['cCacheFullPageContentLifetimeCustom'])) {
			$cCacheFullPageContentLifetimeCustom = $data['cCacheFullPageContentLifetimeCustom'];
		}
		if (isset($data['cCacheFullPageContentOverrideLifetime'])) {
			$cCacheFullPageContentOverrideLifetime = $data['cCacheFullPageContentOverrideLifetime'];
		}
		if (isset($data['cDescription'])) {
			$cDescription = $data['cDescription'];
		}
		if (isset($data['cDatePublic'])) {
			$cDatePublic = $data['cDatePublic'];
		}
		if (isset($data['uID'])) {
			$uID = $data['uID'];
		}
		if (isset($data['ctID'])) {
			$ctID = $data['ctID'];
			// we grab the package that this ct belongs to
			$pkgID = $db->GetOne("select pkgID from PageTypes where ctID = ?", array($data['ctID']));
			$rescanTemplatePermissions = true;
		}

		$txt = Loader::helper('text');
        if (!isset($data['cHandle']) && ($this->getCollectionHandle() != '')) {
            $cHandle = $this->getCollectionHandle();
        } else if (!$data['cHandle']) {
            // make the handle out of the title
            $cHandle = $txt->urlify($cName);
			$cHandle = str_replace('-', PAGE_PATH_SEPARATOR, $cHandle);		
        } else {
            $cHandle = $txt->urlify($data['cHandle']);
			$cHandle = str_replace('-', PAGE_PATH_SEPARATOR, $cHandle);		
        }
		$cName = $txt->sanitize($cName);
		
		// Update the non-canonical page paths
		if (isset($data['ppURL']))
			$this->rescanPagePaths($data['ppURL']);

		if ($this->isGeneratedCollection()) {
			if (isset($data['cFilename'])) {
				$cFilename = $data['cFilename'];
			}
			// we only update a subset
			$v = array($cName, $cHandle, $cDescription, $cDatePublic, $cvID, $this->cID);
			$q = "update CollectionVersions set cvName = ?, cvHandle = ?, cvDescription = ?, cvDatePublic = ? where cvID = ? and cID = ?";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);

		} else {

			$v = array($cName, $cHandle, $ctID, $cDescription, $cDatePublic, $cvID, $this->cID);
			$q = "update CollectionVersions set cvName = ?, cvHandle = ?, ctID = ?, cvDescription = ?, cvDatePublic = ? where cvID = ? and cID = ?";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);				
		}

		$db->query("update Pages set uID = ?, pkgID = ?, cFilename = ?, cCacheFullPageContent = ?, cCacheFullPageContentLifetimeCustom = ?, cCacheFullPageContentOverrideLifetime = ? where cID = ?", array($uID, $pkgID, $cFilename, $cCacheFullPageContent, $cCacheFullPageContentLifetimeCustom, $cCacheFullPageContentOverrideLifetime, $this->cID));

		$cache = PageCache::getLibrary();
		$cache->purge($this);

		$this->refreshCache();
		
		$ret = Events::fire('on_page_update', $this);
	}
	
	public function uniquifyPagePath($origPath) {
		$db = Loader::db();

		$proceed = false;
		$suffix = 0;
		while ($proceed != true) {
			$newPath = ($suffix == 0) ? $origPath : $origPath . $suffix;
			$v = array($newPath, $this->cID);
			$q = "select cID from PagePaths where cPath = ? and cID <> ?";
			$r = $db->query($q, $v);
			if ($r->numRows() == 0) {
				$proceed = true;
			} else {
				$suffix++;
			}
		}

		return $newPath;
	}

	public function rescanPagePaths($newPaths) {
		$db = Loader::db();
		$txt = Loader::helper('text');

		// First, get the list of page paths from the DB.
		$ppaths = $this->getPagePaths();

		// Second, reset all of their cPath values to null.
		$paths = array();
		foreach ($ppaths as $ppath) {
			if (!$ppath['ppIsCanonical']) {
				$paths[$ppath['ppID']] = null;
			}
		}

		// Third, fill in the cPath values from the user updated data.
		Loader::library('3rdparty/urlify');
		foreach ($newPaths as $key=>$val) {
			if (!empty($val)) {
				// Auto-prepend a slash if one is missing.
				$val = trim($val, '/');
				$pathSegments = explode('/', $val);
				$newVal = '/';
				foreach($pathSegments as $pathSegment) {
					$newVal .= $pathSegment . '/';
				}
				$newVal = substr($newVal, 0, strlen($newVal) - 1);
				$newVal = str_replace('-', PAGE_PATH_SEPARATOR, $newVal);

				$paths[$key] = $newVal;
			}
		}
		
		// Fourth, delete, update, or insert page paths as necessary.
		foreach ($paths as $key=>$val) {
			if (empty($val)) {
				$v = array($this->cID, $key);
				$q = "delete from PagePaths where cID = ? and ppID = ?";
			} else if (is_numeric($key)) {
				$val = $this->uniquifyPagePath($val);
				$v = array($val, $this->cID, $key);
				$q = "update PagePaths set cPath = ?, ppIsCanonical = 0 where cID = ? and ppID = ?";
			} else {
				$val = $this->uniquifyPagePath($val);
				$v = array($this->cID, $val);
				$q = "insert into PagePaths (cID, cPath, ppIsCanonical) values (?, ?, 0)";
			}
			$r = $db->query($q, $v);
		}
	}

	function clearPagePermissions() {
		$db = Loader::db();
		$db->Execute("delete from PagePermissionAssignments where cID = '{$this->cID}'");
		$this->permissionAssignments = array();
	}

	public function inheritPermissionsFromParent() {
		$db = Loader::db();
		$cpID = $this->getParentPermissionsCollectionID();
		$this->updatePermissionsCollectionID($this->cID, $cpID);
		$v = array('PARENT', $cpID, $this->cID);
		$q = "update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?";
		$r = $db->query($q, $v);
		$this->cInheritPermissionsFrom = 'PARENT';
		$this->cInheritPermissionsFromCID = $cpID;
		$this->clearPagePermissions();
		$this->rescanAreaPermissions();
	}

	public function inheritPermissionsFromDefaults() {
		$db = Loader::db();
		$cpID = $this->getMasterCollectionID();
		$this->updatePermissionsCollectionID($this->cID, $cpID);
		$v = array('TEMPLATE', $cpID, $this->cID);
		$q = "update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?";
		$r = $db->query($q, $v);
		$this->cInheritPermissionsFrom = 'TEMPLATE';
		$this->cInheritPermissionsFromCID = $cpID;
		$this->clearPagePermissions();
		$this->rescanAreaPermissions();
	}
	
	public function setPermissionsToManualOverride() {
		if ($this->cInheritPermissionsFrom != 'OVERRIDE') { 
			$db = Loader::db();
			$this->acquirePagePermissions($this->getPermissionsCollectionID());
			$this->acquireAreaPermissions($this->getPermissionsCollectionID());

			$cpID = $this->cID;
			$this->updatePermissionsCollectionID($this->cID, $cpID);
			$v = array('OVERRIDE', $cpID, $this->cID);
			$q = "update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?";
			$r = $db->query($q, $v);
			$this->cInheritPermissionsFrom = 'OVERRIDE';
			$this->cInheritPermissionsFromCID = $cpID;
			$this->rescanAreaPermissions();
		}	
	}
	
	public function rescanAreaPermissions() {
		$db = Loader::db();
		$r = $db->Execute('select arHandle, arIsGlobal from Areas where cID = ?', $this->getCollectionID());
		while ($row = $r->FetchRow()) {
			$a = Area::getOrCreate($this, $row['arHandle'], $row['arIsGlobal']);
			$a->rescanAreaPermissionsChain();
		}
	}
	
	public function setOverrideTemplatePermissions($cOverrideTemplatePermissions) {
		$db = Loader::db();
		$v = array($cOverrideTemplatePermissions, $this->cID);
		$q = "update Pages set cOverrideTemplatePermissions = ? where cID = ?";
		$db->Execute($q, $v);
		$this->cOverrideTemplatePermissions = $cOverrideTemplatePermissions;
	}

	function updatePermissionsCollectionID($cParentIDString, $npID) {
		// now we iterate through
		$db = Loader::db();
		$pcID = $this->getPermissionsCollectionID();
		$q = "select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFromCID = {$pcID}";
		$r = $db->query($q);
		$cList = array();
		while ($row = $r->fetchRow()) {
			$cList[] = $row['cID'];
		}
		if (count($cList) > 0) {
			$cParentIDString = implode(',', $cList);
			$q2 = "update Pages set cInheritPermissionsFromCID = {$npID} where cID in ({$cParentIDString})";
			$r2 = $db->query($q2);
			$this->updatePermissionsCollectionID($cParentIDString, $npID);
		}
	}
	

	function acquireAreaPermissions($permissionsCollectionID) {
		$v = array($this->cID);
		$db = Loader::db();
		$q = "delete from AreaPermissionAssignments where cID = ?";
		$db->query($q, $v);

		// ack - we need to copy area permissions from that page as well
		$v = array($permissionsCollectionID);
		$q = "select cID, arHandle, paID, pkID from AreaPermissionAssignments where cID = ?";
		$r = $db->query($q, $v);
		while($row = $r->fetchRow()) {
			$v = array($this->cID, $row['arHandle'], $row['paID'], $row['pkID']);
			$q = "insert into AreaPermissionAssignments (cID, arHandle, paID, pkID) values (?, ?, ?, ?)";
			$db->query($q, $v);
		}

		// any areas that were overriding permissions on the current page need to be overriding permissions
		// on the NEW page as well.
		$v = array($permissionsCollectionID);
		$q = "select * from Areas where cID = ?";
		$r = $db->query($q, $v);
		while($row = $r->fetchRow()) {
			$v = array($this->cID, $row['arHandle'], $row['arOverrideCollectionPermissions'], $row['arInheritPermissionsFromAreaOnCID'], $row['arIsGlobal']);
			$q = "insert into Areas (cID, arHandle, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID, arIsGlobal) values (?, ?, ?, ?, ?)";
			$db->query($q, $v);
		}
	}
	
	function acquirePagePermissions($permissionsCollectionID) {
		$v = array($this->cID);
		$db = Loader::db();
		$q = "delete from PagePermissionAssignments where cID = ?";
		$db->query($q, $v);

		$v = array($permissionsCollectionID);
		$q = "select cID, paID, pkID from PagePermissionAssignments where cID = ?";
		$r = $db->query($q, $v);
		while($row = $r->fetchRow()) {
			$v = array($this->cID, $row['paID'], $row['pkID']);
			$q = "insert into PagePermissionAssignments (cID, paID, pkID) values (?, ?, ?)";
			$db->query($q, $v);
		}
	}

	public function __destruct() {
		parent::__destruct();
	}


	function updateGroupsSubCollection($cParentIDString) {
		// now we iterate through
		$db = Loader::db();
		$pcID = $this->getPermissionsCollectionID();
		$q = "select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFrom = 'PARENT'";
		$r = $db->query($q);
		$cList = array();
		while ($row = $r->fetchRow()) {
			$cList[] = $row['cID'];
		}
		if (count($cList) > 0) {
			$cParentIDString = implode(',', $cList);
			$q2 = "update Pages set cInheritPermissionsFromCID = {$this->cID} where cID in ({$cParentIDString})";
			$r2 = $db->query($q2);
			$this->updateGroupsSubCollection($cParentIDString);
		}
	}
	
	function move($nc, $retainOldPagePath = false) {
		$db = Loader::db();
		$newCParentID = $nc->getCollectionID();
		$dh = Loader::helper('date');

		Loader::model('page_statistics');

		$cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;

		PageStatistics::decrementParents($cID);
		
		$cDateModified = $dh->getSystemDateTime();
		if ($this->getPermissionsCollectionID() != $this->getCollectionID() && $this->getPermissionsCollectionID() != $this->getMasterCollectionID()) {
			// implicitly, we're set to inherit the permissions of wherever we are in the site.
			// as such, we'll change to inherit whatever permissions our new parent has
			$npID = $nc->getPermissionsCollectionID();
			if ($npID != $this->getPermissionsCollectionID()) {
				//we have to update the existing collection with the info for the new
				//as well as all collections beneath it that are set to inherit from this parent
				// first we do this one
				$q = "update Pages set cInheritPermissionsFromCID = {$npID} where cID = {$this->cID}";
				$r = $db->query($q);
				$this->updatePermissionsCollectionID($this->getCollectionID(), $npID);
			}
		}
		
		$db->query("update Collections set cDateModified = ? where cID = ?", array($cDateModified, $cID));
		$v = array($newCParentID, $cID);
		$q = "update Pages set cParentID = ? where cID = ?";
		$r = $db->prepare($q);
		$res = $db->execute($r, $v);

		PageStatistics::incrementParents($cID);
		if (!$this->isActive()) {
			$this->activate();
			// if we're moving from the trash, we have to activate recursively
			if ($this->isInTrash()) {
				$pages = array();
				$pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, false);
				foreach($pages as $page) {
					$db->Execute('update Pages set cIsActive = 1 where cID = ?', array($page['cID']));
				}
			}
		}
		
		$this->rescanSystemPageStatus();
		$this->cParentID = $newCParentID;
		$this->movePageDisplayOrderToBottom();
		// run any event we have for page move. Arguments are
		// 1. current page being moved
		// 2. former parent
		// 3. new parent
		
		$oldParent = Page::getByID($this->getCollectionParentID(), 'RECENT');
		$newParent = Page::getByID($newCParentID, 'RECENT');
		
		$ret = Events::fire('on_page_move', $this, $oldParent, $newParent);

		// now that we've moved the collection, we rescan its path
		$this->rescanCollectionPath($retainOldPagePath);
	}

	function duplicateAll($nc, $preserveUserID = false) {
		$db = Loader::db();
		$nc2 = $this->duplicate($nc);
		Page::_duplicateAll($this, $nc2, $preserveUserID);
		return $nc2;
	}

	/**
	* @access private
	**/

	function _duplicateAll($cParent, $cNewParent, $preserveUserID = false) {
		$db = Loader::db();
		$cID = $cParent->getCollectionID();
		$q = "select cID from Pages where cParentID = '{$cID}' order by cDisplayOrder asc";
		$r = $db->query($q);
		if ($r) {
			while ($row = $r->fetchRow()) {
				$tc = Page::getByID($row['cID']);
				$nc = $tc->duplicate($cNewParent, $preserveUserID);
				$tc->_duplicateAll($tc, $nc, $preserveUserID);
			}
		}
	}

	function duplicate($nc, $preserveUserID = false) {
		$db = Loader::db();
		// the passed collection is the parent collection
		$cParentID = $nc->getCollectionID();

		$u = new User();
		$uID = $u->getUserID();
		if ($preserveUserID) {
			$uID = $this->getCollectionUserID();		
		}
		$dh = Loader::helper('date');			
		$cDate = $dh->getSystemDateTime();
		
		$cobj = parent::getByID($this->cID);
		// create new name
		
		$newCollectionName = $this->getCollectionName();
		$index = 1;
		$nameCount = 1;
		
		while ($nameCount > 0) {
			// if we have a node at the new level with the same name, we keep incrementing til we don't
			$nameCount = $db->GetOne('select count(Pages.cID) from CollectionVersions inner join Pages on (CollectionVersions.cID = Pages.cID and CollectionVersions.cvIsApproved = 1) where Pages.cParentID = ? and CollectionVersions.cvName = ?',
				array($cParentID, $newCollectionName)
			);
			if ($nameCount > 0) {
				$index++;
				$newCollectionName = $this->getCollectionName() . ' ' . $index;
			}
		}
		
		$newC = $cobj->duplicate();
		$newCID = $newC->getCollectionID();
		
		$v = array($newCID, $cParentID, $uID, $this->overrideTemplatePermissions(), $this->getPermissionsCollectionID(), $this->getCollectionInheritance(), $this->cFilename, $this->cPointerID, $this->cPointerExternalLink, $this->cPointerExternalLinkNewWindow, $this->cDisplayOrder, $this->pkgID);
		$q = "insert into Pages (cID, cParentID, uID, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cInheritPermissionsFrom, cFilename, cPointerID, cPointerExternalLink, cPointerExternalLinkNewWindow, cDisplayOrder, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$res = $db->query($q, $v);
	
		Loader::model('page_statistics');
		PageStatistics::incrementParents($newCID);
		
		if ($res) {
			// rescan the collection path
			$nc2 = Page::getByID($newCID);

			// now with any specific permissions - but only if this collection is set to override
			if ($this->getCollectionInheritance() == 'OVERRIDE') {
				$nc2->acquirePagePermissions($this->getPermissionsCollectionID());
				$nc2->acquireAreaPermissions($this->getPermissionsCollectionID());
				// make sure we update the proper permissions pointer to the new page ID
				$q = "update Pages set cInheritPermissionsFromCID = ? where cID = ?";
				$v = array($newCID, $newCID);
				$r = $db->query($q, $v);
			} else if ($this->getCollectionInheritance() == "PARENT") {
				// we need to clear out any lingering permissions groups (just in case), and set this collection to inherit from the parent
				$npID = $nc->getPermissionsCollectionID();
				$q = "update Pages set cInheritPermissionsFromCID = {$npID} where cID = {$newCID}";
				$r = $db->query($q);
			}
			
			if ($index > 1) {
				$args['cName'] = $newCollectionName;
				$args['cHandle'] = $nc2->getCollectionHandle() . '-' . $index;
			}
			$nc2->update($args);
			
			// arguments for event
			// 1. new page
			// 2. old page
			$ret = Events::fire('on_page_duplicate', $nc2, $this);
			
			$nc2->rescanCollectionPath();

			return $nc2;
		}
	}

	function delete() {
		Loader::model('page_statistics');
		$cID = $this->getCollectionID();

		if ($cID <= 1) {
			return false;
		}

		$db = Loader::db();

		// run any internal event we have for page deletion
		$ret = Events::fire('on_page_delete', $this);

		if ($ret < 0) {
			return false;
		}
		Log::addEntry(t('Page "%s" at path "%s" deleted', $this->getCollectionName(), $this->getCollectionPath()),t('Page Action'));

		parent::delete();
		
		$cID = $this->getCollectionID();
		$cParentID = $this->getCollectionParentID();

		// Now that all versions are gone, we can delete the collection information
		$q = "delete from PagePaths where cID = '{$cID}'";
		$r = $db->query($q);
		
		// remove all pages where the pointer is this cID
		$r = $db->query("select cID from Pages where cPointerID = ?", array($cID));
		while ($row = $r->fetchRow()) {
			PageStatistics::decrementParents($row['cID']);
			$db->Execute('DELETE FROM PagePaths WHERE cID=?', array($row['cID']));
		}

		// Update cChildren for cParentID
		PageStatistics::decrementParents($cID);
		
		$q = "delete from PagePermissionAssignments where cID = '{$cID}'";
		$r = $db->query($q);

		$q = "delete from Pages where cID = '{$cID}'";
		$r = $db->query($q);

		$q = "delete from Pages where cPointerID = '{$cID}'";
		$r = $db->query($q);

		$q = "delete from Areas WHERE cID = '{$cID}'";
		$r = $db->query($q);

		$q = "delete from ComposerDrafts WHERE cID = '{$cID}'";
		$r = $db->query($q);

		$db->query('delete from PageSearchIndex where cID = ?', array($cID));
		
		$q = "select cID from Pages where cParentID = '{$cID}'";
		$r = $db->query($q);
		if ($r) {
			while ($row = $r->fetchRow()) {
				if ($row['cID'] > 0) {
					$nc = Page::getByID($row['cID']);  
					if( $nc->isAlias() )
						 $nc->removeThisAlias(); 
					else $nc->delete();
				}
			}
		}

		$cache = PageCache::getLibrary();
		$cache->purge($this);

	}
	
	public function moveToTrash() {
		$trash = Page::getByPath(TRASH_PAGE_PATH);
		Log::addEntry(t('Page "%s" at path "%s" Moved to trash', $this->getCollectionName(), $this->getCollectionPath()),t('Page Action'));
		$this->move($trash);
		$this->deactivate();
		$pages = array();
		$pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, false);
		$db = Loader::db();
		foreach($pages as $page) {
			$db->Execute('update Pages set cIsActive = 0 where cID = ?', array($page['cID']));
		}
	}

	function rescanChildrenDisplayOrder() {
		$db = Loader::db();
		// this should be re-run every time a new page is added, but i don't think it is yet - AE
		//$oneLevelOnly=1;
		//$children_array = $this->getCollectionChildrenArray( $oneLevelOnly );
		$q = "SELECT cID FROM Pages WHERE cParentID=".intval($this->getCollectionID()).' ORDER BY cDisplayOrder';
		$children_array = $db->getCol($q);
		$current_count=0;
		foreach($children_array as $newcID) {
			$q = "update Pages set cDisplayOrder='$current_count' where cID='$newcID'";
			$r = $db->query($q);
			$current_count++;
		}
	}
	
	function getNextSubPageDisplayOrder() {
		$db = Loader::db();
		$max = $db->getOne("select max(cDisplayOrder) from Pages where cParentID = " . $this->getCollectionID());
		if ($max == "" || $max == null) {
			return 0;
		} else if (!$max) {
			return 1;
		} else {
			return $max + 1;
		}
		
	}

	function rescanCollectionPath($retainOldPagePath = false) {
		if ($this->cParentID > 0) {
			$db = Loader::db();
			// first, we grab the path of the parent, if such a thing exists, for our prefix
			$q = "select PagePaths.cPath as cPathParent from PagePaths left join Pages on (Pages.cParentID = PagePaths.cID and PagePaths.ppIsCanonical = 1) where Pages.cID = '{$this->cID}'";
			$cPath = $db->getOne($q);

			// Now we perform the collection path function on the current cID
			$np = $this->rescanCollectionPathIndividual($this->cID, $cPath, $retainOldPagePath);
			$this->cPath = $np;
			
			// Now we start with the recursive collection path scanning, armed with our prefix (from the level above what we're scanning)
			if ($np) {
				$this->rescanCollectionPathChildren($this->cID, $np);
			}
		}
	}
	
	function updateDisplayOrder($do,$cID=0) {
		//this line was added to allow changing the display order of aliases
		if(!intval($cID)) $cID=$this->getCollectionID();
		$db = Loader::db();
		$db->query("update Pages set cDisplayOrder = ? where cID = ?", array($do, $cID));
	}
	
	public function movePageDisplayOrderToTop() {
		// first, we take the current collection, stick it at the beginning of an array, then get all other items from the current level that aren't that cID, order by display order, and then update
		$db = Loader::db();
		$nodes = array();
		$nodes[] = $this->getCollectionID();
		$r = $db->GetCol('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', array($this->getCollectionParentID(), $this->getCollectionID()));
		$nodes = array_merge($nodes, $r);
		$displayOrder = 0;
		foreach($nodes as $do) {
			$co = Page::getByID($do);
			$co->updateDisplayOrder($displayOrder);
			$displayOrder++;			
		}
	}
	
	public function movePageDisplayOrderToBottom() {
		// first, we take the current collection, stick it at the beginning of an array, then get all other items from the current level that aren't that cID, order by display order, and then update
		$db = Loader::db();
		$nodes = $db->GetCol('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', array($this->getCollectionParentID(), $this->getCollectionID()));
		$displayOrder = 0;
		$nodes[] = $this->getCollectionID();
		foreach($nodes as $do) {
			$co = Page::getByID($do);
			$co->updateDisplayOrder($displayOrder);
			$displayOrder++;			
		}
	}
	
	function rescanCollectionPathIndividual($cID, $cPath, $retainOldPagePath = false) {
		$db = Loader::db();
		$q = "select CollectionVersions.cID, CollectionVersions.cvHandle, CollectionVersions.cvID, PagePaths.cID as cpcID from CollectionVersions left join PagePaths on (PagePaths.cID = CollectionVersions.cID) where CollectionVersions.cID = '{$cID}' and CollectionVersions.cvIsApproved = 1";
		$r = $db->query($q);
		if (!$r) return;

		$row = $r->fetchRow();
		if (!$row['cvHandle']) {
			$row['cvHandle'] = $row['cID'];
		}
		if ($row['cvHandle']) {
			$origPath = $cPath . '/' . $row['cvHandle'];

			// first, we check to see if this path already exists
			$proceed = false;
			$suffix = 0;
			while ($proceed != true) {
				$newPath = ($suffix == 0) ? $origPath : $origPath . $suffix;
				$v2 = array($newPath);
				$q2 = "select cID from PagePaths where cPath = ? and cID <> {$cID}";
				$r2 = $db->query($q2, $v2);
				if ($r2->numRows() == 0) {
					$proceed = true;
				} else {
					$suffix++;
				}
			}

			if ($row['cpcID']) {
				if ($retainOldPagePath) {
					$db->query("update PagePaths set ppIsCanonical = 0 where cID = {$cID}");
				} else {
					$db->query('delete from PagePaths where ppIsCanonical = 1 and cID = ?', array($row['cpcID']));
				}
			}

			// Check to see if a non-canonical page path already exists for the new location.
			$v = array($cID, $newPath);
			$rc = $db->query("select cID from PagePaths where cID = ? and cPath = ?", $v);
			if ($rc->numRows() > 0) {
  				// Update the non-canonical path to be canonical.
				$q3 = "update PagePaths set ppIsCanonical = 1 where cID = ? and cPath = ?";
			} else {
				// Create a new page path for the new location.
				$q3 = "insert into PagePaths (cID, cPath) values (?, ?)";
			}
			$rc->free();
			$r3 = $db->prepare($q3);
			$res3 = $db->execute($r3, $v);
			
			if ($res3) {
				$np = Page::getByID($cID, $row['cvID']);
				$np->rescanSystemPageStatus();
				return $newPath;
			}
		}
		$r->free();
	}
	
	public function rescanSystemPageStatus() {
		$cID = $this->getCollectionID();
		$db = Loader::db();
		$newPath = $db->GetOne('select cPath from PagePaths where cID = ? and ppIsCanonical = 1', array($cID));
		// now we mark the page as a system page based on this path:
		$systemPages=array('/login', '/register', '/!trash', '/!stacks', '/!drafts', '/!trash/*', '/!stacks/*', '/!drafts/*', '/download_file', '/profile', '/dashboard', '/profile/*', '/dashboard/*','/page_forbidden','/page_not_found','/members'); 
		$th = Loader::helper('text');
		$db->Execute('update Pages set cIsSystemPage = 0 where cID = ?', array($cID));
		foreach($systemPages as $sp) {
			if ($th->fnmatch($sp, $newPath)) {
				$db->Execute('update Pages set cIsSystemPage = 1 where cID = ?', array($cID));
			}
		}				
	}
	
	public function isInTrash() {
		return $this->getCollectionPath() != TRASH_PAGE_PATH && strpos($this->getCollectionPath(), TRASH_PAGE_PATH) === 0;
	}
	
	public function moveToRoot() {
		$db = Loader::db();
		$db->Execute('update Pages set cParentID = 0 where cID = ?', array($this->getCollectionID()));
	}

	public function rescanSystemPages() {
		$db = Loader::db();
		$systemPages=array('/login', '/register', '/!trash/%', '/!drafts/%', '/!stacks/%', '/!trash', '/!stacks', '/!drafts', '/download_file', '/profile', '/dashboard', '/profile/%', '/dashboard/%','/page_forbidden','/page_not_found','/members'); 
		foreach($systemPages as $sp) {
			$r = $db->Execute('select cID from PagePaths where cPath like "' . $sp . '"');
			while ($row = $r->Fetchrow()) {
				$db->Execute('update Pages set cIsSystemPage = 1 where cID = ?', array($row['cID']));
			}			
		}
	}
	
	public function deactivate() {
		$db = Loader::db();
		$db->Execute('update Pages set cIsActive = 0 where cID = ?', array($this->getCollectionID()));
	}

	public function activate() {
		$db = Loader::db();
		$db->Execute('update Pages set cIsActive = 1 where cID = ?', array($this->getCollectionID()));
	}
	
	public function isActive() {
		return $this->cIsActive;
	}
	
	public function setPageIndexScore($score) {
		$this->cIndexScore = $score;
	}
	
	public function getPageIndexScore() {
		return round($this->cIndexScore, 2);
	}

	public function getPageIndexContent() {
		$db = Loader::db();
		return $db->GetOne('select content from PageSearchIndex where cID = ?', array($this->cID));
	}


	function rescanCollectionPathChildren($cID, $cPath) {
		$db = Loader::db();
		$q = "select cID from Pages where cParentID = $cID";
		$r = $db->query($q);
		if ($r) {
			while ($row = $r->fetchRow()) {
				$np = $this->rescanCollectionPathIndividual($row['cID'], $cPath);
				$this->rescanCollectionPathChildren($row['cID'], $np);
			}
			$r->free();
		}
	}

	function getCollectionAction() {
		$cID = $this->cID;
		$valt = Loader::helper('validation/token');
		$token = $valt->getParameter();
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && defined('BASE_URL_SSL')) {
			$str = BASE_URL_SSL . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&" . $token;
		} else {
			$str = BASE_URL . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID={$cID}&" . $token;
		}
		return $str;
	}


	function _associateMasterCollectionBlocks($newCID, $masterCID) {
		$mc = Page::getByID($masterCID, 'ACTIVE');
		$nc = Page::getByID($newCID, 'RECENT');
		$db = Loader::db();

		$mcID = $mc->getCollectionID();
		$mcvID = $mc->getVersionID();

		$q = "select CollectionVersionBlocks.arHandle, BlockTypes.btCopyWhenPropagate, CollectionVersionBlocks.cbOverrideAreaPermissions, CollectionVersionBlocks.bID from CollectionVersionBlocks inner join Blocks on Blocks.bID = CollectionVersionBlocks.bID inner join BlockTypes on Blocks.btID = BlockTypes.btID where CollectionVersionBlocks.cID = '$mcID' and CollectionVersionBlocks.cvID = '{$mcvID}' order by CollectionVersionBlocks.cbDisplayOrder asc";

		// ok. This function takes two IDs, the ID of the newly created virgin collection, and the ID of the crusty master collection
		// who will impart his wisdom to the his young learner, by duplicating his various blocks, as well as their permissions, for the
		// new collection


		//$q = "select CollectionBlocks.cbAreaName, Blocks.bID, Blocks.bName, Blocks.bFilename, Blocks.btID, Blocks.uID, BlockTypes.btClassname, BlockTypes.btTablename from CollectionBlocks left join BlockTypes on (Blocks.btID = BlockTypes.btID) inner join Blocks on (CollectionBlocks.bID = Blocks.bID) where CollectionBlocks.cID = '$masterCID' order by CollectionBlocks.cbDisplayOrder asc";
		//$q = "select CollectionVersionBlocks.cbAreaName, Blocks.bID, Blocks.bName, Blocks.bFilename, Blocks.btID, Blocks.uID, BlockTypes.btClassname, BlockTypes.btTablename from CollectionBlocks left join BlockTypes on (Blocks.btID = BlockTypes.btID) inner join Blocks on (CollectionBlocks.bID = Blocks.bID) where CollectionBlocks.cID = '$masterCID' order by CollectionBlocks.cbDisplayOrder asc";

		$r = $db->query($q);

		if ($r) {
			while ($row = $r->fetchRow()) {
				$b = Block::getByID($row['bID'], $mc, $row['arHandle']);
				if ($row['btCopyWhenPropagate']) {
					$b->duplicate($nc);
				} else {
					$b->alias($nc);
				}
			}
			$r->free();
		}
	}
	
	function _associateMasterCollectionAttributes($newCID, $masterCID) {
		$mc = Page::getByID($masterCID, 'ACTIVE');
		$nc = Page::getByID($newCID, 'RECENT');
		$db = Loader::db();

		$mcID = $mc->getCollectionID();
		$mcvID = $mc->getVersionID();

		$q = "select * from CollectionAttributeValues where cID = ?";
		$r = $db->query($q, array($mcID));

		if ($r) {
			while ($row = $r->fetchRow()) {
				$db->Execute('insert into CollectionAttributeValues (cID, cvID, akID, avID) values (?, ?, ?, ?)', array(
					$nc->getCollectionID(), $nc->getVersionID(), $row['akID'], $row['avID']
				));
			}
			$r->free();
		}
	}
	
	/**
	* Adds the home page to the system. Typically used only by the installation program.
	* @return page
	**/

	public static function addHomePage() {
		// creates the home page of the site
		Loader::model('collection_types');
		$dh = Loader::helper('date');
		$db = Loader::db();
		
		// we use to hard code the home page page type into the system
		// but now we're not going to do that
		
		//$db->query("insert into PageTypes (ctID, ctHandle, ctName) values (?, ?, ?)", array(HOME_CTID, HOME_HANDLE, HOME_NAME));
		
		$cParentID = 0;
		$handle = HOME_HANDLE;
		$uID = HOME_UID;
		$name = HOME_NAME;
		
		$data['name'] = HOME_NAME;
		$data['handle'] = $handle;
		$data['uID'] = $uID;
		$data['cID'] = HOME_CID;

		$cobj = parent::add($data);		
		$cID = $cobj->getCollectionID();
		
		//$ctID = HOME_CTID;
		$ctID = 0;
		
		$cDate = $dh->getSystemDateTime();
		$cDatePublic = $dh->getSystemDateTime();
		
		$v = array($cID, $cParentID, $uID, 'OVERRIDE', 1, 1, 0);
		$q = "insert into Pages (cID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder) values (?, ?, ?, ?, ?, ?, ?)";
		$r = $db->prepare($q);
		$res = $db->execute($r, $v);
		$pc = Page::getByID($cID, 'RECENT');
		return $pc;
	}
	
	/**
	* Adds a new page of a certain type, using a passed associate array to setup value. $data may contain any or all of the following:
	* "uID": User ID of the page's owner
	* "pkgID": Package ID the page belongs to
	* "cName": The name of the page
	* "cHandle": The handle of the page as used in the path
	* "cDatePublic": The date assigned to the page
	* @param collectiontype $ct
	* @param array $data
	* @return page
	**/
	
	public function add(CollectionType $ct, $data) {
		$db = Loader::db();
		$txt = Loader::helper('text');
		
		// the passed collection is the parent collection
		$cParentID = $this->getCollectionID();

		$u = new User();
		if (isset($data['uID'])) {
			$uID = $data['uID'];
		} else {
			$uID = $u->getUserID();
			$data['uID'] = $uID;
		}
		
		if (isset($data['pkgID'])) {
			$pkgID = $data['pkgID'];
		} else if ($ct->getPackageID() > 0) {
			$pkgID = $ct->getPackageID();
		} else {
			$pkgID = 0;
		}

		if (isset($data['cName'])) {
			$data['name'] = $data['cName'];
		}
		
		if (!$data['cHandle']) {
			// make the handle out of the title
			$handle = $txt->urlify($data['name']);
		} else {
			$handle = $txt->urlify($data['cHandle']);
		}
		$handle = str_replace('-', PAGE_PATH_SEPARATOR, $handle);		
		$data['handle'] = $handle;
		$dh = Loader::helper('date');
		$cDate = $dh->getSystemDateTime();
		$cDatePublic = ($data['cDatePublic']) ? $data['cDatePublic'] : null;		
		
		$data['ctID'] = $ct->getCollectionTypeID();
		if ($ct->getCollectionTypeHandle() == STACKS_PAGE_TYPE) {
			$data['cvIsNew'] = 0;
		}
		$cobj = parent::add($data);		
		$cID = $cobj->getCollectionID();		
		$ctID = $ct->getCollectionTypeID();

		$q = "select p.cID from Pages p inner join CollectionVersions cv on p.cID = cv.cID where cv.ctID = '$ctID' and cIsTemplate = '1'";
		$masterCID = $db->getOne($q);
		//$this->rescanChildrenDisplayOrder();
		$cDisplayOrder = $this->getNextSubPageDisplayOrder();

		$cInheritPermissionsFromCID = ($this->overrideTemplatePermissions()) ? $this->getPermissionsCollectionID() : $masterCID;
		$cInheritPermissionsFrom = ($this->overrideTemplatePermissions()) ? "PARENT" : "TEMPLATE";
		$ptID = $this->getCollectionThemeID();
		$v = array($cID, $cParentID, $uID, $cInheritPermissionsFrom, $this->overrideTemplatePermissions(), $cInheritPermissionsFromCID, $cDisplayOrder, $pkgID);
		$q = "insert into Pages (cID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?)";
		$r = $db->prepare($q);
		$res = $db->execute($r, $v);

		$newCID = $cID;

		if ($res) {
			// Collection added with no problem -- update cChildren on parrent
			Loader::model('page_statistics');
			PageStatistics::incrementParents($newCID);

			if ($r) {
				// now that we know the insert operation was a success, we need to see if the collection type we're adding has a master collection associated with it
				if ($masterCID) {
					$this->_associateMasterCollectionBlocks($newCID, $masterCID);
					$this->_associateMasterCollectionAttributes($newCID, $masterCID);
				}
			}
			
			$pc = Page::getByID($newCID, 'RECENT');

			// run any internal event we have for page addition
			Events::fire('on_page_add', $pc);
			$pc->rescanCollectionPath();


		}
		
		return $pc;
	}
	
	public function getCollectionFullPageCaching() {
		return $this->cCacheFullPageContent;
	}
	
	public function getCollectionFullPageCachingLifetime() {
		return $this->cCacheFullPageContentOverrideLifetime;
	}
	
	public function getCollectionFullPageCachingLifetimeCustomValue() {
		return $this->cCacheFullPageContentLifetimeCustom;
	}

	public function getCollectionFullPageCachingLifetimeValue() {
		if ($this->cCacheFullPageContentOverrideLifetime == 'default') {
			$lifetime = CACHE_LIFETIME;
		} else if ($this->cCacheFullPageContentOverrideLifetime == 'custom') {
			$lifetime = $this->cCacheFullPageContentLifetimeCustom * 60;		
		} else if ($this->cCacheFullPageContentOverrideLifetime == 'forever') {
			$lifetime = 31536000; // 1 year
		} else {
			if (FULL_PAGE_CACHE_LIFETIME == 'custom') {
				$lifetime = Config::get('FULL_PAGE_CACHE_LIFETIME_CUSTOM') * 60;		
			} else if (FULL_PAGE_CACHE_LIFETIME == 'forever') {
				$lifetime = 31536000; // 1 year
			} else {
				$lifetime = CACHE_LIFETIME;
			}
		}

		if (!$lifetime) {
			// we have no value, which means forever, but we need a numerical value for page caching
			$lifetime = 31536000;
		}
		
		return $lifetime;
	}
	
	public function addStatic($data) {
		$db = Loader::db();
		$cParentID = $this->getCollectionID();
		
		if (isset($data['pkgID'])) {
			$pkgID = $data['pkgID'];
		} else {
			$pkgID = 0;
		}
		
		$handle = $data['handle'];
		$cName = $data['name'];
		$cFilename = $data['filename'];
		
		$uID = USER_SUPER_ID;
		$data['uID'] = $uID;
		$cIsSystemPage = 0;
		$cobj = parent::add($data);		
		$cID = $cobj->getCollectionID();
		
		$this->rescanChildrenDisplayOrder();
		$cDisplayOrder = $this->getNextSubPageDisplayOrder();

		// These get set to parent by default here, but they can be overridden later
		$cInheritPermissionsFromCID = $this->getPermissionsCollectionID();
		$cInheritPermissionsFrom = 'PARENT';
		
		$v = array($cID, $cFilename, $cParentID, $cInheritPermissionsFrom, $this->overrideTemplatePermissions(), $cInheritPermissionsFromCID, $cDisplayOrder, $cIsSystemPage, $uID, $pkgID);
		$q = "insert into Pages (cID, cFilename, cParentID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, cIsSystemPage, uID, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$r = $db->prepare($q);
		$res = $db->execute($r, $v);

		if ($res) {
			// Collection added with no problem -- update cChildren on parrent
			Loader::model('page_statistics');
			PageStatistics::incrementParents($cID);
		}
			

		$pc = Page::getByID($cID);
		$pc->rescanCollectionPath();
		return $pc;
				
	}
	
	function getPagePaths() {
		$db = Loader::db();

		$q = "select ppID, cPath, ppIsCanonical from PagePaths where cID = {$this->cID}";
		$r = $db->query($q, $v);
		$paths = array();
		if ($r) {
			while ($row = $r->fetchRow()) {
				$paths[] = $row;
			}
			$r->free();
		}

		return $paths;
	}

	/*
	 * returns an instance of the current page object 
	 *
	*/
	public static function getCurrentPage() {
		$req = Request::get();
		$current = $req->getCurrentPage();
		if (is_object($current)) {
			return $current;
		} else {
			global $c;
			return $c;
		}
	}

}
