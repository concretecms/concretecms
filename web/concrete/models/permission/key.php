<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class PermissionKey extends Object {
	

	const ACCESS_TYPE_INCLUDE = 1;
	const ACCESS_TYPE_EXCLUDE = -1;
	
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
	

	protected static function load($pkID) {
		$db = Loader::db();
		$r = $db->GetRow('select pkID, pkName, pkDescription, pkHandle, pkCategoryHandle, PermissionKeys.pkgID from PermissionKeys inner join PermissionKeyCategories on PermissionKeyCategories.pkCategoryID = PermissionKeys.pkCategoryID where pkID = ?', array($pkID));
		$class = Loader::helper('text')->unhandle($r['pkCategoryHandle']) . 'PermissionKey';
		if (!is_array($r) && (!$r['pkID'])) { 
			return false;
		}
		
		if ($r['pkgID'] > 0) {
			$pkgHandle = PackageList::getHandle($r['pkgID']);	
			$file1 = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $r['pkHandle'] . '.php';
			$file2 = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $r['pkHandle'] . '.php';
			if (file_exists($file1)) {
				require_once($file1);
				$class = Loader::helper('text')->camelcase($r['pkHandle']) . $class;
			} else if (file_exists($file2)) {
				require_once($file2);
				$class = Loader::helper('text')->camelcase($r['pkHandle']) . $class;
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
	
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	public function getPermissionKeyToolsURL($task = false) {
		if (!$task) {
			$task = 'save_permission';
		}
		$uh = Loader::helper('concrete/urls');
		$url = $uh->getToolsURL('permissions/categories/' . $this->pkCategoryHandle, $this->getPackageHandle());
		$token = Loader::helper('validation/token')->getParameter($task);
		$url .= '?' . $token . '&task=' . $task . '&pkID=' . $this->getPermissionKeyID();
		return $url;
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
	
	public function export($axml, $exporttype = 'full') {
		$category = PermissionKeyCategory::getByID($this->pkCategoryID)->getPermissionKeyCategoryHandle();
		$pkey = $axml->addChild('permissionkey');
		$pkey->addAttribute('handle',$this->getPermissionKeyHandle());
		if ($exporttype == 'full') { 
			$pkey->addAttribute('name', $this->getPermissionKeyName());
			$pkey->addAttribute('description', $this->getPermissionKeyDescription());
			$pkey->addAttribute('package', $this->getPackageHandle());
			$pkey->addAttribute('category', $category);
			$this->getController()->exportKey($pkey);
		}
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
		$pkn = self::add($pkCategoryHandle, $pk['handle'], $pk['name'], $pk['description'], $pkg);
	}
	
	/** 
	 * Adds an permission key. 
	 */
	protected function add($pkCategoryHandle, $pkHandle, $pkName, $pkDescription, $pkg = false) {
		
		$vn = Loader::helper('validation/numbers');
		$txt = Loader::helper('text');
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}

		$db = Loader::db();
		$pkCategoryID = $db->GetOne("select pkCategoryID from PermissionKeyCategories where pkCategoryHandle = ?", $pkCategoryHandle);
		$a = array($pkHandle, $pkName, $pkDescription, $pkCategoryID, $pkgID);
		$r = $db->query("insert into PermissionKeys (pkHandle, pkName, pkDescription, pkCategoryID, pkgID) values (?, ?, ?, ?, ?)", $a);
		
		$category = AttributeKeyCategory::getByID($pkCategoryID);
		
		if ($r) {
			$pkID = $db->Insert_ID();
			$className = $txt->camelcase($pkCategoryHandle) . 'PermissionKey';
			$ak = new $className();
			$ak->load($pkID);
			return $ak;
		}
	}

	public function delete() {
	
		$db = Loader::db();
		$db->Execute('delete from PermissionKeys where pkID = ?', array($this->getPermissionKeyID()));

	}
	
	abstract public function getAssignmentList($accessType = false);
	

	

}