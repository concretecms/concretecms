<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AttributeKeyCategory extends Object {

	const ASET_ALLOW_NONE = 0;
	const ASET_ALLOW_SINGLE = 1;
	const ASET_ALLOW_MULTIPLE = 2;
	
	public static function getByID($akCategoryID) {
		$db = Loader::db();
		$row = $db->GetRow('select akCategoryID, akCategoryHandle, akCategoryAllowSets, pkgID from AttributeKeyCategories where akCategoryID = ?', array($akCategoryID));
		if (isset($row['akCategoryID'])) {
			$akc = new AttributeKeyCategory();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}
	
	public static function getByHandle($akCategoryHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select akCategoryID, akCategoryHandle, akCategoryAllowSets, pkgID from AttributeKeyCategories where akCategoryHandle = ?', array($akCategoryHandle));
		if (isset($row['akCategoryID'])) {
			$akc = new AttributeKeyCategory();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}
	
	public function handleExists($akHandle) {
		$db = Loader::db();
		$r = $db->GetOne("select count(akID) from AttributeKeys where akHandle = ? and akCategoryID = ?", array($akHandle, $this->akCategoryID));
		return $r > 0;
	}

	public static function exportList($xml) {
		$attribs = self::getList();		
		$axml = $xml->addChild('attributecategories');
		foreach($attribs as $akc) {
			$acat = $axml->addChild('category');
			$acat->addAttribute('handle', $akc->getAttributeKeyCategoryHandle());
			$acat->addAttribute('allow-sets', $akc->allowAttributeSets());
			$acat->addAttribute('package', $akc->getPackageHandle());
		}		
	}
	
	public function getAttributeKeyByHandle($akHandle) {
		if ($this->pkgID > 0) {
			Loader::model('attribute/categories/' . $this->akCategoryHandle, $this->getPackageHandle());
		} else {
			Loader::model('attribute/categories/' . $this->akCategoryHandle);
		}		
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->akCategoryHandle);
		$c1 = $className . 'AttributeKey';
		$ak = call_user_func(array($c1, 'getByHandle'), $akHandle);
		return $ak;
	}

	public function getAttributeKeyByID($akID) {
		if ($this->pkgID > 0) {
			Loader::model('attribute/categories/' . $this->akCategoryHandle, $this->getPackageHandle());
		} else {
			Loader::model('attribute/categories/' . $this->akCategoryHandle);
		}		
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->akCategoryHandle);
		$c1 = $className . 'AttributeKey';
		$ak = call_user_func(array($c1, 'getByID'), $akID);
		return $ak;
	}

	public function getUnassignedAttributeKeys() {
		$db = Loader::db();
		$r = $db->Execute('select AttributeKeys.akID from AttributeKeys left join AttributeSetKeys on AttributeKeys.akID = AttributeSetKeys.akID where asID is null and akIsInternal = 0 and akCategoryID = ?', $this->akCategoryID);
		$keys = array();
		$cat = AttributeKeyCategory::getByID($this->akCategoryID);
		while ($row = $r->FetchRow()) {
			$keys[] = $cat->getAttributeKeyByID($row['akID']);
		}
		return $keys;		
	}	

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select akCategoryID from AttributeKeyCategories where pkgID = ? order by akCategoryID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = AttributeKeyCategory::getByID($row['akCategoryID']);
		}
		$r->Close();
		return $list;
	}	
	
	public function getAttributeKeyCategoryID() {return $this->akCategoryID;}
	public function getAttributeKeyCategoryHandle() {return $this->akCategoryHandle;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}
	public function allowAttributeSets() {return $this->akCategoryAllowSets;}
	public function setAllowAttributeSets($val) {
		$db = Loader::db();
		$db->Execute('update AttributeKeyCategories set akCategoryAllowSets = ? where akCategoryID = ?', array($val, $this->akCategoryID));
		$this->akCategoryAllowSets = $val;
	}
	
	public function getAttributeSets() {
		$db = Loader::db();
		$r = $db->Execute('select asID from AttributeSets where akCategoryID = ? order by asDisplayOrder asc, asID asc', $this->akCategoryID);
		$sets = array();
		while ($row = $r->FetchRow()) {
			$sets[] = AttributeSet::getByID($row['asID']);
		}
		return $sets;
	}
	
	public function clearAttributeKeyCategoryColumnHeaders() {
		$db = Loader::db();
		$db->Execute('update AttributeKeys set akIsColumnHeader = 0 where akCategoryID = ?', $this->akCategoryID);
	}
	
	public function associateAttributeKeyType($at) {
		if (!$this->hasAttributeKeyTypeAssociated($at)) {
			$db = Loader::db();
			$db->Execute('insert into AttributeTypeCategories (atID, akCategoryID) values (?, ?)', array($at->getAttributeTypeID(), $this->akCategoryID));
		}
	}

	public function hasAttributeKeyTypeAssociated($at) {
		$db = Loader::db();
		$r = $db->getOne('select atID from AttributeTypeCategories where atID = ? and akCategoryID = ?', array($at->getAttributeTypeID(), $this->akCategoryID));
		return (boolean) $r;
	}
	
	public function clearAttributeKeyCategoryTypes() {
		$db = Loader::db();
		$db->Execute('delete from AttributeTypeCategories where akCategoryID = ?', $this->akCategoryID);
	}

	/** 
	 * note, this does not remove anything but the direct data associated with the category
	 */
	public function delete() {
		$db = Loader::db();
		$this->clearAttributeKeyCategoryTypes();
		$this->clearAttributeKeyCategoryColumnHeaders();
		$this->rescanSetDisplayOrder();
		$db->Execute('delete from AttributeKeyCategories where akCategoryID = ?', $this->akCategoryID);		
	}
	
	public static function getList() {
		$db = Loader::db();
		$cats = array();
		$r = $db->Execute('select akCategoryID from AttributeKeyCategories order by akCategoryID asc');
		while ($row = $r->FetchRow()) {
			$cats[] = AttributeKeyCategory::getByID($row['akCategoryID']);
		}
		return $cats;
	}
	
	public static function add($akCategoryHandle, $akCategoryAllowSets = AttributeKeyCategory::ASET_ALLOW_NONE, $pkg = false) {
		$db = Loader::db();
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db->Execute('insert into AttributeKeyCategories (akCategoryHandle, akCategoryAllowSets, pkgID) values (?, ?, ?)', array($akCategoryHandle, $akCategoryAllowSets, $pkgID));
		$id = $db->Insert_ID();
		
		if ($pkgID > 0) {
			Loader::model('attribute/categories/' . $akCategoryHandle, $pkg->getPackageHandle());
		} else {
			Loader::model('attribute/categories/' . $akCategoryHandle);
		}		
		$txt = Loader::helper("text");
		$class = $txt->camelcase($akCategoryHandle) . 'AttributeKey';
		$obj = new $class;
		$obj->createIndexedSearchTable();
		
		return AttributeKeyCategory::getByID($id);
	}

	public function addSet($asHandle, $asName, $pkg = false, $asIsLocked = 1) {
		if ($this->akCategoryAllowSets > AttributeKeyCategory::ASET_ALLOW_NONE) {
			$db = Loader::db();
			$pkgID = 0;
			if (is_object($pkg)) {
				$pkgID = $pkg->getPackageID();
			}
			$sets = $db->GetOne('select count(asID) from AttributeSets where akCategoryID = ?', array($this->akCategoryID));
			$asDisplayOrder = 0;
			if ($sets > 0) {
				$asDisplayOrder = $db->GetOne('select max(asDisplayOrder) from AttributeSets where akCategoryID = ?', array($this->akCategoryID));
				$asDisplayOrder++;
			}
			
			$db->Execute('insert into AttributeSets (asHandle, asName, akCategoryID, asIsLocked, asDisplayOrder, pkgID) values (?, ?, ?, ?, ?,?)', array($asHandle, $asName, $this->akCategoryID, $asIsLocked, $asDisplayOrder, $pkgID));
			$id = $db->Insert_ID();
			
			$as = AttributeSet::getByID($id);
			return $as;
		}
	}
	
	protected function rescanSetDisplayOrder() {
		$db = Loader::db();
		$do = 1;
		$r = $db->Execute('select asID from AttributeSets where akCategoryID = ? order by asDisplayOrder asc, asID asc', $this->getAttributeKeyCategoryID());
		while ($row = $r->FetchRow()) {
			$db->Execute('update AttributeSetKeys set displayOrder = ? where asID = ?', array($do, $row['asID']));
			$do++;
		}
	}

	public function updateAttributeSetDisplayOrder($uats) {
		$db = Loader::db();
		for ($i = 0; $i < count($uats); $i++) {
			$v = array($this->getAttributeKeyCategoryID(), $uats[$i]);
			$db->query("update AttributeSets set asDisplayOrder = {$i} where akCategoryID = ? and asID = ?", $v);
		}
	}
	
}
