<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeKeyCategory extends Object {

	public static function getByID($akCategoryID) {
		$db = Loader::db();
		$row = $db->GetRow('select akCategoryID, akCategoryHandle from AttributeKeyCategories where akCategoryID = ?', array($akCategoryID));
		if (isset($row['akCategoryID'])) {
			$akc = new AttributeKeyCategory();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}
	
	public static function getByHandle($akCategoryHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select akCategoryID, akCategoryHandle from AttributeKeyCategories where akCategoryHandle = ?', array($akCategoryHandle));
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
	
	public function getAttributeKeyCategoryID() {return $this->akCategoryID;}
	public function getAttributeKeyCategoryHandle() {return $this->akCategoryHandle;}
	
	public function clearAttributeKeyCategoryColumnHeaders() {
		$db = Loader::db();
		$db->Execute('update AttributeKeys set akIsColumnHeader = 0 where akCategoryID = ?', $this->akCategoryID);
	}
	
	public function associateAttributeKeyType($at) {
		$db = Loader::db();
		$db->Execute('insert into AttributeTypeCategories (atID, akCategoryID) values (?, ?)', array($at->getAttributeTypeID(), $this->akCategoryID));
	}
	
	public function clearAttributeKeyCategoryTypes() {
		$db = Loader::db();
		$db->Execute('delete from AttributeTypeCategories where akCategoryID = ?', $this->akCategoryID);
	}
		
	public function getList() {
		$db = Loader::db();
		$cats = array();
		$r = $db->Execute('select akCategoryID from AttributeKeyCategories order by akCategoryID asc');
		while ($row = $r->FetchRow()) {
			$cats[] = AttributeKeyCategory::getByID($row['akCategoryID']);
		}
		return $cats;
	}
	
	public static function add($akCategoryHandle, $pkg = false) {
		$db = Loader::db();
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		$db->Execute('insert into AttributeKeyCategories (akCategoryHandle, pkgID) values (?, ?)', array($akCategoryHandle, $pkgID));
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

}
