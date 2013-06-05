<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Contains the file attribute key and value objects.
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
/**
 * An object that represents metadata added to files. 
 * @author Andrew Embler <andrew@concrete5.org>
 * @package Pages
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Model_FileAttributeKey extends AttributeKey {

	public function getIndexedSearchTable() {
		return 'FileSearchIndexAttributes';
	}

	protected $searchIndexFieldDefinition = 'fID I(11) UNSIGNED NOTNULL DEFAULT 0 PRIMARY';
	
	/** 
	 * Returns an attribute value list of attributes and values (duh) which a collection version can store 
	 * against its object.
	 * @return AttributeValueList
	 */
	public function getAttributes($fID, $fvID, $method = 'getValue') {
		$db = Loader::db();
		$values = $db->GetAll("select akID, avID from FileAttributeValues where fID = ? and fvID = ?", array($fID, $fvID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = FileAttributeKey::getByID($val['akID']);
			if (is_object($ak)) {
				$value = $ak->getAttributeValue($val['avID'], $method);
				$avl->addAttributeValue($ak, $value);
			}
		}		
		return $avl;
	}
	
	public function getAttributeValue($avID, $method = 'getValue') {
		$av = FileAttributeValue::getByID($avID);
		if (is_object($av)) {
			$av->setAttributeKey($this);
			return $av->{$method}();
		}
	}

	public static function getByHandle($akHandle) {
		$ak = CacheLocal::getEntry('file_attribute_key_by_handle', $akHandle);
		if (is_object($ak)) {
			return $ak;
		} else if ($ak == -1) {
			return false;
		}
		
		$ak = -1;
		$db = Loader::db();
		$q = "SELECT ak.akID FROM AttributeKeys ak INNER JOIN AttributeKeyCategories akc ON ak.akCategoryID = akc.akCategoryID  WHERE ak.akHandle = ? AND akc.akCategoryHandle = 'file'";
		$akID = $db->GetOne($q, array($akHandle));
		if ($akID > 0) {
			$ak = self::getByID($akID);
		} else {
			 // else we check to see if it's listed in the initial registry
			 $ia = FileTypeList::getImporterAttribute($akHandle);
			 if (is_object($ia)) {
			 	// we create this attribute and return it.
			 	$at = AttributeType::getByHandle($ia->akType);
				$args = array(
					'akHandle' => $akHandle,
					'akName' => $ia->akName,
					'akIsSearchable' => 1,
					'akIsAutoCreated' => 1,
					'akIsEditable' => $ia->akIsEditable
				);
			 	$ak = FileAttributeKey::add($at, $args);
			 }
		}
		CacheLocal::set('file_attribute_key_by_handle', $akHandle, $ak);
		if ($ak === -1) {
			return false;
		}
		return $ak;
	}

	public static function getByID($akID) {
		$ak = new FileAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}
	

	public static function getList() {
		return parent::getList('file');	
	}

	public static function getSearchableList() {
		return parent::getList('file', array('akIsSearchable' => 1));	
	}
	public static function getSearchableIndexedList() {
		return parent::getList('file', array('akIsSearchableIndexed' => 1));	
	}

	public static function getImporterList($fv = false) {
		$list = parent::getList('file', array('akIsAutoCreated' => 1));	
		if ($fv == false) {
			return $list;
		}
		$list2 = array();
		$db = Loader::db();
		foreach($list as $l) {
			$r = $db->GetOne('select count(akID) from FileAttributeValues where fID = ? and fvID = ? and akID = ?', array($fv->getFileID(), $fv->getFileVersionID(), $l->getAttributeKeyID()));
			if ($r > 0) {
				$list2[] = $l;
			}
		}
		return $list2;
	}

	public static function getUserAddedList() {
		return parent::getList('file', array('akIsAutoCreated' => 0));	
	}
	
	/** 
	 * @access private 
	 */
	public function get($akID) {
		return FileAttributeKey::getByID($akID);
	}
	
	protected function saveAttribute($f, $value = false) {
		// We check a cID/cvID/akID combo, and if that particular combination has an attribute value ID that
		// is NOT in use anywhere else on the same cID, cvID, akID combo, we use it (so we reuse IDs)
		// otherwise generate new IDs
		$av = $f->getAttributeValueObject($this, true);
		parent::saveAttribute($av, $value);
		$db = Loader::db();
		$v = array($f->getFileID(), $f->getFileVersionID(), $this->getAttributeKeyID(), $av->getAttributeValueID());
		$db->Replace('FileAttributeValues', array(
			'fID' => $f->getFileID(), 
			'fvID' => $f->getFileVersionID(), 
			'akID' => $this->getAttributeKeyID(), 
			'avID' => $av->getAttributeValueID()
		), array('fID', 'fvID', 'akID'));
		$f->logVersionUpdate(FileVersion::UT_EXTENDED_ATTRIBUTE, $this->getAttributeKeyID());
		$fo = $f->getFile();
		$fo->reindex();
		unset($av);
		unset($fo);
		unset($f);
	}

	public function add($at, $args, $pkg = false) {
		CacheLocal::delete('file_attribute_key_by_handle', $args['akHandle']);
		$ak = parent::add('file', $at, $args, $pkg);
		return $ak;
	}
	
	public static function getColumnHeaderList() {
		return parent::getList('file', array('akIsColumnHeader' => 1));	
	}

	
	public function delete() {
		parent::delete();
		$db = Loader::db();
		$r = $db->Execute('select avID from FileAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
		}
		$db->Execute('delete from FileAttributeValues where akID = ?', array($this->getAttributeKeyID()));
	}

}

class Concrete5_Model_FileAttributeValue extends AttributeValue {

	public function setFile($f) {
		$this->f = $f;
	}
	
	public static function getByID($avID) {
		$fav = new FileAttributeValue();
		$fav->load($avID);
		if ($fav->getAttributeValueID() == $avID) {
			return $fav;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from FileAttributeValues where fID = ? and fvID = ? and akID = ? and avID = ?', array(
			$this->f->getFileID(), 
			$this->f->getFileVersionID(),
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));

		// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
		$num = $db->GetOne('select count(avID) from FileAttributeValues where avID = ?', array($this->getAttributeValueID()));
		if ($num < 1) {
			parent::delete();
		}
	}
}