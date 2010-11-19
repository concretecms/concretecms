<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class AttributeSet extends Object {

	public static function getByID($asID) {
		$db = Loader::db();
		$row = $db->GetRow('select asID, asHandle, pkgID, asName, akCategoryID from AttributeSets where asID = ?', array($asID));
		if (isset($row['asID'])) {
			$akc = new AttributeSet();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}
	
	public static function getByHandle($asHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select asID, asHandle, pkgID, asName, akCategoryID from AttributeSets where asHandle = ?', array($asHandle));
		if (isset($row['asID'])) {
			$akc = new AttributeSet();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select asID from AttributeSets where pkgID = ? order by asID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = AttributeSet::getByID($row['asID']);
		}
		$r->Close();
		return $list;
	}	
	
	public function getAttributeSetID() {return $this->asID;}
	public function getAttributeSetHandle() {return $this->asHandle;}
	public function getAttributeSetName() {return $this->asName;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}
	public function getAttributeSetKeyCategoryID() {return $this->akCategoryID;}
	
	public function addKey($ak) {
		$db = Loader::db();
		$no = $db->GetOne("select count(akID) from AttributeSetKeys where akID = ? and asID = ?", array($ak->getAttributeKeyID(), $this->getAttributeSetID()));
		if ($no < 1) {
			$do = $db->GetOne('select max(displayOrder) from AttributeSetKeys where asID = ?', $this->getAttributeSetID());
			$do++;
			$db->Execute('insert into AttributeSetKeys (asID, akID, displayOrder) values (?, ?, ?)', array($this->getAttributeSetID(), $ak->getAttributeKeyID(), $do));
		}
	}

	public function getAttributeKeys() {
		$db = Loader::db();
		$r = $db->Execute('select akID from AttributeSetKeys where asID = ? order by displayOrder asc', $this->getAttributeSetID());
		$keys = array();
		$cat = AttributeKeyCategory::getByID($this->akCategoryID);
		while ($row = $r->FetchRow()) {
			$ak = $cat->getAttributeKeyByID($row['akID']);
			if (is_object($ak)) {
				$keys[] = $ak;
			}
		}
		return $keys;		
	}
	
	public function contains($ak) {
		$db = Loader::db();
		$r = $db->GetOne('select count(akID) from AttributeSetKeys where asID = ? and akID = ?', array($this->getAttributeSetID(), $ak->getAttributeKeyID()));
		return $r > 0;
	}	
	
	/** 
	 * Removes an attribute set and sets all keys within to have a set ID of 0.
	 */
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from AttributeSets where asID = ?', array($this->getAttributeSetID()));
		$db->Execute('delete from AttributeSetKeys where asID = ?', array($this->getAttributeSetID()));
	}
	
	public function deleteKey($ak) {
		$db = Loader::db();
		$db->Execute('delete from AttributeSetKeys where asID = ? and akID = ?', array($this->getAttributeSetID(), $ak->getAttributeKeyID()));
		$this->rescanDisplayOrder();
	}
	
	protected function rescanDisplayOrder() {
		$db = Loader::db();
		$do = 1;
		$r = $db->Execute('select akID from AttributeSetKeys where asID = ? order by displayOrder asc', $this->getAttributeSetID());
		while ($row = $r->FetchRow()) {
			$db->Execute('update AttributeSetKeys set displayOrder = ? where akID = ? and asID = ?', array($do, $row['akID'], $this->getAttributeSetID()));
			$do++;
		}
	}

	public function updateAttributesDisplayOrder($uats) {
		$db = Loader::db();
		for ($i = 0; $i < count($uats); $i++) {
			$v = array($this->getAttributeSetID(), $uats[$i]);
			$db->query("update AttributeSetKeys set displayOrder = {$i} where asID = ? and akID = ?", $v);
		}
	}


		
}
