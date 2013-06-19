<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PermissionAccessEntityType extends Object {

	public function getAccessEntityTypeID() {return $this->petID;}
	public function getAccessEntityTypeHandle() {return $this->petHandle;}
	public function getAccessEntityTypeName() {return $this->petName;}
	public function getAccessEntityTypeClass() {
		$class = Loader::helper('text')->camelcase($this->petHandle) . 'PermissionAccessEntity';
		return $class;
	}
	public static function getByID($petID) {
		$db = Loader::db();
		$row = $db->GetRow('select petID, pkgID, petHandle, petName from PermissionAccessEntityTypes where petID = ?', array($petID));
		if ($row['petHandle']) {
			$wt = new PermissionAccessEntityType();
			$wt->setPropertiesFromArray($row);
			return $wt;
		}
	}
	
	public function __call($method, $args) {
		$obj = $this->getAccessEntityTypeClass();
		$o = new $obj();
		return call_user_func_array(array($obj, $method), $args);
	}
	
	public function getAccessEntityTypeToolsURL($task = false) {
		if (!$task) {
			$task = 'process';
		}
		$uh = Loader::helper('concrete/urls');
		$url = $uh->getToolsURL('permissions/access/entity/types/' . $this->petHandle, $this->getPackageHandle());
		$token = Loader::helper('validation/token')->getParameter($task);
		$url .= '?' . $token . '&task=' . $task;
		return $url;
	}

	public static function getList($category = false) {
		$db = Loader::db();
		$list = array();
		if ($category instanceof PermissionKeyCategory) {
			$r = $db->Execute('select pet.petID from PermissionAccessEntityTypes pet inner join PermissionAccessEntityTypeCategories petc on pet.petID = petc.petID where petc.pkCategoryID = ? order by pet.petID asc', array($category->getPermissionKeyCategoryID()));
		} else { 
			$r = $db->Execute('select petID from PermissionAccessEntityTypes order by petID asc');
		}
		
		while ($row = $r->FetchRow()) {
			$list[] = PermissionAccessEntityType::getByID($row['petID']);
		}
		
		$r->Close();
		return $list;
	}

	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	public static function exportList($xml) {
		$ptypes = PermissionAccessEntityType::getList();
		$db = Loader::db();
		$axml = $xml->addChild('permissionaccessentitytypes');
		foreach($ptypes as $pt) {
			$ptype = $axml->addChild('permissionaccessentitytype');
			$ptype->addAttribute('handle', $pt->getAccessEntityTypeHandle());
			$ptype->addAttribute('name', tc('PermissionAccessEntityTypeName', $pt->getAccessEntityTypeName()));
			$ptype->addAttribute('package', $pt->getPackageHandle());
			$categories = $db->GetCol('select pkCategoryHandle from PermissionKeyCategories inner join PermissionAccessEntityTypeCategories where PermissionKeyCategories.pkCategoryID = PermissionAccessEntityTypeCategories.pkCategoryID and PermissionAccessEntityTypeCategories.petID = ?', array($pt->getAccessEntityTypeID()));
			if (count($categories) > 0) {
				$cat = $ptype->addChild('categories');
				foreach($categories as $catHandle) {
					$cat->addChild('category')->addAttribute('handle', $catHandle);
				}
			}
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute("delete from PermissionAccessEntityTypes where petID = ?", array($this->petID));
	}
	
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select petID from PermissionAccessEntityTypes where pkgID = ? order by petID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = PermissionAccessEntityType::getByID($row['petID']);
		}
		$r->Close();
		return $list;
	}	
	
	public static function getByHandle($petHandle) {
		$db = Loader::db();
		$petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = ?', array($petHandle));
		if ($petID > 0) {
			return self::getByID($petID);
		}
	}
	
	public static function add($petHandle, $petName, $pkg = false) {
		$pkgID = 0;
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db = Loader::db();
		$db->Execute('insert into PermissionAccessEntityTypes (petHandle, petName, pkgID) values (?, ?, ?)', array($petHandle, $petName, $pkgID));
		$id = $db->Insert_ID();
		$est = PermissionAccessEntityType::getByID($id);
		return $est;
	}
	
}
