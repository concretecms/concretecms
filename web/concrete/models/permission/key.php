<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class PermissionKey extends Object {
	
	const ACCESS_TYPE_INCLUDE = 10;
	const ACCESS_TYPE_EXCLUDE = -1;
	const ACCESS_TYPE_ALL = 0;
	
	public function getSupportedAccessTypes() {
		$types = array(
			self::ACCESS_TYPE_INCLUDE => t('Included'),
			self::ACCESS_TYPE_EXCLUDE => t('Excluded'),
		);
		return $types;
	}
	
	/** 
	 * Returns whether a permission key can start a workflow
	 */
	public function canPermissionKeyTriggerWorkflow() {return $this->pkCanTriggerWorkflow;}
	
	/** 
	 * Returns the name for this permission key
	 */
	public function getPermissionKeyName() { return $this->pkName;}

	/** 
	 * Returns the handle for this permission key
	 */
	public function getPermissionKeyHandle() { return $this->pkHandle;}

	/** 
	 * Returns the description for this permission key
	 */
	public function getPermissionKeyDescription() { return $this->pkDescription;}
	
	/** 
	 * Returns the ID for this permission key
	 */
	public function getPermissionKeyID() {return $this->pkID;}
	public function getPermissionKeyCategoryID() {return $this->pkCategoryID;}
	public function getPermissionKeyCategoryHandle() {return $this->pkCategoryHandle;}
	
	public function setPermissionObject($object) {
		$this->permissionObject = $object;
	}
	
	public function getPermissionObjectToCheck() {
		if (is_object($this->permissionObjectToCheck)) {
			return $this->permissionObjectToCheck;
		} else {
			return $this->permissionObject;
		}
	}
	
	public function getPermissionObject() {
		return $this->permissionObject;
	}

	protected static function load($pkID) {
		$db = Loader::db();
		$r = $db->GetRow('select pkID, pkName, pkDescription, pkHandle, pkCategoryHandle, pkCanTriggerWorkflow, PermissionKeys.pkCategoryID, pkCategoryHandle, PermissionKeys.pkgID from PermissionKeys inner join PermissionKeyCategories on PermissionKeyCategories.pkCategoryID = PermissionKeys.pkCategoryID where pkID = ?', array($pkID));
		$class = Loader::helper('text')->camelcase($r['pkCategoryHandle']) . 'PermissionKey';
		if (!is_array($r) && (!$r['pkID'])) { 
			return false;
		}
		
		if ($r['pkgID'] > 0) {
			$pkgHandle = PackageList::getHandle($r['pkgID']);	
			$file1 = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $r['pkHandle'] . '.php';
			$file2 = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $r['pkHandle'] . '.php';
			$file3 = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/categories/' . $r['pkCategoryHandle'] . '.php';
			$file4 = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/categories/' . $r['pkCategoryHandle'] . '.php';
			if (file_exists($file1)) {
				require_once($file1);
				$class = Loader::helper('text')->camelcase($r['pkHandle']) . $class;
			} else if (file_exists($file2)) {
				require_once($file2);
				$class = Loader::helper('text')->camelcase($r['pkHandle']) . $class;
			} else if (file_exists($file3)) {
				require_once($file3);
			} else if (file_exists($file4)) {
				require_once($file4);
			}			
		} else {
			$file1 = DIR_BASE . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $r['pkHandle'] . '.php';
			$file2 = DIR_BASE_CORE . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $r['pkHandle'] . '.php';
			if (file_exists($file1)) {
				require_once($file1);
				$class = Loader::helper('text')->camelcase($r['pkHandle']) . $class;
			} else if (file_exists($file2)) {
				require_once($file2);
				$class = Loader::helper('text')->camelcase($r['pkHandle']) . $class;
			}			
		}
		$pk = new $class();
		$pk->setPropertiesFromArray($r);
		return $pk;
	}
	
	public function hasCustomOptionsForm() {
		$file = Loader::helper('concrete/path')->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $this->pkHandle . '.php', $this->pkgID);
		return $file != false;
	}
	
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	/** 
	 * Returns a list of all permissions of this category
	 */
	public static function getList($pkCategoryHandle, $filters = array()) {
		$db = Loader::db();
		$q = 'select pkID from PermissionKeys inner join PermissionKeyCategories on PermissionKeys.pkCategoryID = PermissionKeyCategories.pkCategoryID where pkCategoryHandle = ?';
		foreach($filters as $key => $value) {
			$q .= ' and ' . $key . ' = ' . $value . ' ';
		}
		$r = $db->Execute($q, array($pkCategoryHandle));
		$list = array();
		while ($row = $r->FetchRow()) {
			$pk = self::load($row['pkID']);
			if (is_object($pk)) {
				$list[] = $pk;
			}
		}
		$r->Close();
		return $list;
	}
	
	public function export($axml) {
		$category = PermissionKeyCategory::getByID($this->pkCategoryID)->getPermissionKeyCategoryHandle();
		$pkey = $axml->addChild('permissionkey');
		$pkey->addAttribute('handle',$this->getPermissionKeyHandle());
		$pkey->addAttribute('name', $this->getPermissionKeyName());
		$pkey->addAttribute('description', $this->getPermissionKeyDescription());
		$pkey->addAttribute('package', $this->getPackageHandle());
		$pkey->addAttribute('category', $category);
		$this->exportAccess($pkey);
		return $pkey;
	}

	public static function exportList($xml) {
		$categories = PermissionKeyCategory::getList();
		$pxml = $xml->addChild('permissionkeys');
		foreach($categories as $cat) {
			$permissions = PermissionKey::getList($cat->getPermissionKeyCategoryHandle());
			foreach($permissions as $p) {
				$p->export($pxml);
			}
		}
	}
	
	/** 
	 * Note, this queries both the pkgID found on the PermissionKeys table AND any permission keys of a special type
	 * installed by that package, and any in categories by that package.
	 */
	public static function getListByPackage($pkg) {
		$db = Loader::db();

		$kina[] = '-1';
		$kinb = $db->GetCol('select pkCategoryID from PermissionKeyCategories where pkgID = ?', $pkg->getPackageID());
		if (is_array($kinb)) {
			$kina = array_merge($kina, $kinb);
		}
		$kinstr = implode(',', $kina);


		$r = $db->Execute('select pkID, pkCategoryID from PermissionKeys where (pkgID = ? or pkCategoryID in (' . $kinstr . ')) order by pkID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$pkc = PermissionKeyCategory::getByID($row['pkCategoryID']);
			$pk = $pkc->getPermissionKeyByID($row['pkID']);
			$list[] = $pk;
		}
		$r->Close();
		return $list;
	}	
	
	public static function import(SimpleXMLElement $pk) {
		$pkCategoryHandle = $pk['category'];
		$pkg = false;
		if ($pk['package']) {
			$pkg = Package::getByHandle($pk['package']);
		}
		$pkCanTriggerWorkflow = 0;
		if ($pk['can-trigger-workflow']) {
			$pkCanTriggerWorkflow = 1;
		}
		$pkn = self::add($pkCategoryHandle, $pk['handle'], $pk['name'], $pk['description'], $pkCanTriggerWorkflow, $pkg);
		return $pkn;
	}

	public static function getByID($pkID) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			return $pk;
		}
	}

	public static function getByHandle($pkHandle) {
		$db = Loader::db();
		$pkID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($pkHandle));
		if ($pkID) { 
			$pk = self::load($pkID);
			if ($pk->getPermissionKeyID() > 0) {
				return $pk;
			}
		}
	}
	
	/** 
	 * Adds an permission key. 
	 */
	public function add($pkCategoryHandle, $pkHandle, $pkName, $pkDescription, $pkCanTriggerWorkflow, $pkg = false) {
		
		$vn = Loader::helper('validation/numbers');
		$txt = Loader::helper('text');
		$pkgID = 0;
		$db = Loader::db();
		
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		
		if ($pkCanTriggerWorkflow) {
			$pkCanTriggerWorkflow = 1;
		} else {
			$pkCanTriggerWorkflow = 0;
		}
		$pkCategoryID = $db->GetOne("select pkCategoryID from PermissionKeyCategories where pkCategoryHandle = ?", $pkCategoryHandle);
		$a = array($pkHandle, $pkName, $pkDescription, $pkCategoryID, $pkCanTriggerWorkflow, $pkgID);
		$r = $db->query("insert into PermissionKeys (pkHandle, pkName, pkDescription, pkCategoryID, pkCanTriggerWorkflow, pkgID) values (?, ?, ?, ?, ?, ?)", $a);
		
		$category = PermissionKeyCategory::getByID($pkCategoryID);
		
		if ($r) {
			$pkID = $db->Insert_ID();
			$ak = self::load($pkID);
			return $ak;
		}
	}

	/** 
	 * @access private
	 * legacy support
	 */
	public function can() {
		return $this->validate();
	}
	
	public function validate() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		$pae = $this->getPermissionAccessObject();
		if (is_object($pae)) {
			return $pae->validate();
		} else {
			return false;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from PermissionKeys where pkID = ?', array($this->getPermissionKeyID()));
	}
	
	
	/**
	 * A shortcut for grabbing the current assignment and passing into that object
	 */
	public function getAccessListItems() {
		$args = func_get_args();
		$obj = $this->getPermissionAccessObject();
		if (is_object($obj)) {
			return call_user_func_array(array($obj, 'getAccessListItems'), $args);		
		} else {
			return array();
		}
	}
	
	public function getPermissionAssignmentObject() {
		if (is_object($this->permissionObject)) {
			$class = Loader::helper('text')->camelcase(get_class($this->permissionObject) . 'PermissionAssignment');
			$targ = new $class();
			$targ->setPermissionObject($this->permissionObject);
		} else {
			$targ = new PermissionAssignment();
		}
		$targ->setPermissionKeyObject($this);
		return $targ;
	}

	
	public function getPermissionAccessObject() {
		$targ = $this->getPermissionAssignmentObject();
		return $targ->getPermissionAccessObject();
	}
	
	public function getPermissionAccessID() {
		$pa = $this->getPermissionAccessObject();
		if (is_object($pa)) {
			return $pa->getPermissionAccessID();
		}
	}

	public function exportAccess($pxml) {
		// by default we don't. but tasks do
	}

	

}