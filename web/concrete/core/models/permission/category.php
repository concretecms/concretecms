<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PermissionKeyCategory extends Object {

	public static function getByID($pkCategoryID) {
		$db = Loader::db();
		$row = $db->GetRow('select pkCategoryID, pkCategoryHandle, pkgID from PermissionKeyCategories where pkCategoryID = ?', array($pkCategoryID));
		if (isset($row['pkCategoryID'])) {
			$pkc = new PermissionKeyCategory();
			$pkc->setPropertiesFromArray($row);
			return $pkc;
		}
	}
	
	public static function getByHandle($pkCategoryHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select pkCategoryID, pkCategoryHandle, pkgID from PermissionKeyCategories where pkCategoryHandle = ?', array($pkCategoryHandle));
		if (isset($row['pkCategoryID'])) {
			$pkc = new PermissionKeyCategory();
			$pkc->setPropertiesFromArray($row);
			return $pkc;
		}
	}
	
	public function handleExists($pkHandle) {
		$db = Loader::db();
		$r = $db->GetOne("select count(pkID) from PermissionKeys where pkHandle = ?", array($pkHandle));
		return $r > 0;
	}

	public static function exportList($xml) {
		$attribs = self::getList();		
		$axml = $xml->addChild('permissioncategories');
		foreach($attribs as $pkc) {
			$acat = $axml->addChild('category');
			$acat->addAttribute('handle', $pkc->getPermissionKeyCategoryHandle());
			$acat->addAttribute('package', $pkc->getPackageHandle());
		}		
	}
	
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select pkCategoryID from PermissionKeyCategories where pkgID = ? order by pkCategoryID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = PermissionKeyCategory::getByID($row['pkCategoryID']);
		}
		$r->Close();
		return $list;
	}	

	public function getPermissionKeyByHandle($pkHandle) {
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->pkCategoryHandle);
		$c1 = $className . 'PermissionKey';
		$ak = call_user_func(array($c1, 'getByHandle'), $pkHandle);
		return $ak;
	}

	public function getPermissionKeyByID($pkID) {
		if ($this->pkgID > 0) {
			Loader::model('permission/categories/' . $this->pkCategoryHandle, $this->getPackageHandle());
		} else {
			Loader::model('permission/categories/' . $this->pkCategoryHandle);
		}		
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->pkCategoryHandle);
		$c1 = $className . 'PermissionKey';
		$ak = call_user_func(array($c1, 'getByID'), $pkID);
		return $ak;
	}
	
	public function getToolsURL($task = false) {
		if (!$task) {
			$task = 'save_permission';
		}
		$uh = Loader::helper('concrete/urls');
		$akc = PermissionKeyCategory::getByID($this->getPermissionKeyCategoryID());
		$url = $uh->getToolsURL('permissions/categories/' . $this->pkCategoryHandle, $akc->getPackageHandle());
		$token = Loader::helper('validation/token')->getParameter($task);
		$url .= '?' . $token . '&task=' . $task;
		return $url;
	}

	public function getPermissionKeyCategoryID() {return $this->pkCategoryID;}
	public function getPermissionKeyCategoryHandle() {return $this->pkCategoryHandle;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from PermissionKeyCategories where pkCategoryID = ?', array($this->pkCategoryID));
	}

	public function associateAccessEntityType(PermissionAccessEntityType $pt) {
		$db = Loader::db();
		$db->Execute('insert into PermissionAccessEntityTypeCategories (petID, pkCategoryID) values (?, ?)', array($pt->getAccessEntityTypeID(), $this->pkCategoryID));
	}
	
	public function clearAccessEntityTypeCategories() {
		$db = Loader::db();
		$db->Execute('delete from PermissionAccessEntityTypeCategories where pkCategoryID = ?', $this->pkCategoryID);
	}
	
	public static function getList() {
		$db = Loader::db();
		$cats = array();
		$r = $db->Execute('select pkCategoryID from PermissionKeyCategories order by pkCategoryID asc');
		while ($row = $r->FetchRow()) {
			$cats[] = PermissionKeyCategory::getByID($row['pkCategoryID']);
		}
		return $cats;
	}
	
	public static function add($pkCategoryHandle, $pkg = false) {
		$db = Loader::db();
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db->Execute('insert into PermissionKeyCategories (pkCategoryHandle, pkgID) values (?, ?)', array($pkCategoryHandle, $pkgID));
		$id = $db->Insert_ID();
		
		return PermissionKeyCategory::getByID($id);
	}
	


}
