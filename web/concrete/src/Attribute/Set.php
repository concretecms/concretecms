<?php
namespace Concrete\Core\Attribute;
use \Concrete\Core\Foundation\Object;
use Gettext\Translations;
use Loader;
use \Concrete\Core\Package\PackageList;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;

class Set extends Object {

	public static function getByID($asID) {
		$db = Loader::db();
		$row = $db->GetRow('select asID, asHandle, pkgID, asName, akCategoryID, asIsLocked  from AttributeSets where asID = ?', array($asID));
		if (isset($row['asID'])) {
			$akc = new static();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}

	public static function getByHandle($asHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select asID, asHandle, pkgID, asName, akCategoryID, asIsLocked from AttributeSets where asHandle = ?', array($asHandle));
		if (isset($row['asID'])) {
			$akc = new static();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select asID from AttributeSets where pkgID = ? order by asID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['asID']);
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
	public function isAttributeSetLocked() {return $this->asIsLocked;}

	/** Returns the display name for this attribute set (localized and escaped accordingly to $format)
	* @param string $format = 'html'
	*	Escape the result in html format (if $format is 'html').
	*	If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getAttributeSetDisplayName($format = 'html') {
		$value = tc('AttributeSetName', $this->getAttributeSetName());
		switch($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}

	public function updateAttributeSetName($asName) {
		$this->asName = $asName;
		$db = Loader::db();
		$db->Execute("update AttributeSets set asName = ? where asID = ?", array($asName, $this->asID));
	}

	public function updateAttributeSetHandle($asHandle) {
		$this->asHandle = $asHandle;
		$db = Loader::db();
		$db->Execute("update AttributeSets set asHandle = ? where asID = ?", array($asHandle, $this->asID));
	}

	public function addKey($ak) {
		$db = Loader::db();
		$no = $db->GetOne("select count(akID) from AttributeSetKeys where akID = ? and asID = ?", array($ak->getAttributeKeyID(), $this->getAttributeSetID()));
		if ($no < 1) {
			$do = $db->GetOne('select max(displayOrder) from AttributeSetKeys where asID = ?', array($this->getAttributeSetID()));
			$do++;
			$db->Execute('insert into AttributeSetKeys (asID, akID, displayOrder) values (?, ?, ?)', array($this->getAttributeSetID(), $ak->getAttributeKeyID(), $do));
		}
	}

	public function clearAttributeKeys() {
		$db = Loader::db();
		$db->Execute('delete from AttributeSetKeys where asID = ?', array($this->asID));
	}

	public function export($axml) {
		$category = AttributeKeyCategory::getByID($this->getAttributeSetKeyCategoryID())->getAttributeKeyCategoryHandle();
		$akey = $axml->addChild('attributeset');
		$akey->addAttribute('handle',$this->getAttributeSetHandle());
		$akey->addAttribute('name', $this->getAttributeSetName());
		$akey->addAttribute('package', $this->getPackageHandle());
		$akey->addAttribute('locked', $this->isAttributeSetLocked());
		$akey->addAttribute('category', $category);
		$keys = $this->getAttributeKeys();
		foreach($keys as $ak) {
			$ak->export($akey, false);
		}
		return $akey;
	}

	public static function exportList($xml) {
		$axml = $xml->addChild('attributesets');
		$db = Loader::db();
		$r = $db->Execute('select asID from AttributeSets order by asID asc');
		$list = array();
		while ($row = $r->FetchRow()) {
			$list[] = static::getByID($row['asID']);
		}
		foreach($list as $as) {
			$as->export($axml);
		}
	}

	public function getAttributeKeys() {
		$db = Loader::db();
		$r = $db->Execute('select akID from AttributeSetKeys where asID = ? order by displayOrder asc', array($this->getAttributeSetID()));
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
		$r = $db->Execute('select akID from AttributeSetKeys where asID = ? order by displayOrder asc', array($this->getAttributeSetID()));
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

    public static function exportTranslations()
    {
        $translations = new Translations();
        $db = \Database::get();
        $r = $db->Execute('select asID from AttributeSets order by asID asc');
        while ($row = $r->FetchRow()) {
            $set = static::getByID($row['asID']);
            $translations->insert('AttributeSet', $set->getAttributeSetName());
        }
        return $translations;
    }


}
