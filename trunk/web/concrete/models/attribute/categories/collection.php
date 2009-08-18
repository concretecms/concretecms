<?
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * Contains the collection attribute key and value objects.
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents metadata added to pages. They key object maps to the "type"
 * of metadata added to pages.
 * @author Andrew Embler <andrew@concrete5.org>
 * @package Pages
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class CollectionAttributeKey extends AttributeKey {

	protected function getIndexedSearchTable() {
		return 'CollectionSearchIndexAttributes';
	}

	/** 
	 * Returns an attribute value list of attributes and values (duh) which a collection version can store 
	 * against its object.
	 * @return AttributeValueList
	 */
	public function getAttributes($cID, $cvID, $method = 'getValue') {
		$db = Loader::db();
		$values = $db->GetAll("select akID, avID from CollectionAttributeValues where cID = ? and cvID = ?", array($cID, $cvID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = CollectionAttributeKey::getByID($val['akID']);
			if (is_object($ak)) {
				$value = $ak->getAttributeValue($val['avID'], $method);
				$avl->addAttributeValue($ak, $value);
			}
		}
		return $avl;
	}
	
	public static function getColumnHeaderList() {
		return self::getList('collection', array('akIsColumnHeader' => 1));	
	}

	public function getAttributeValue($avID, $method = 'getValue') {
		$av = CollectionAttributeValue::getByID($avID);
		$av->setAttributeKey($this);
		return call_user_func_array(array($av, $method), array());
	}
	
	public static function getByID($akID) {
		$ak = new CollectionAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
		return $ak;
	}

	public static function getByHandle($akHandle) {
		$db = Loader::db();
		$akID = $db->GetOne('select akID from AttributeKeys where akHandle = ?', array($akHandle));
		$ak = new CollectionAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}
	
	public static function getList() {
		return parent::getList('collection');	
	}
	
	/** 
	 * @access private 
	 */
	public function get($akID) {
		return CollectionAttributeKey::getByID($akID);
	}
	
	protected function saveAttribute($nvc, $value = false) {
		// We check a cID/cvID/akID combo, and if that particular combination has an attribute value ID that
		// is NOT in use anywhere else on the same cID, cvID, akID combo, we use it (so we reuse IDs)
		// otherwise generate new IDs
		$av = $nvc->getAttributeValueObject($this, true);
		parent::saveAttribute($av, $value);
		$db = Loader::db();
		$v = array($nvc->getCollectionID(), $nvc->getVersionID(), $this->getAttributeKeyID(), $av->getAttributeValueID());
		$db->Replace('CollectionAttributeValues', array(
			'cID' => $nvc->getCollectionID(), 
			'cvID' => $nvc->getVersionID(), 
			'akID' => $this->getAttributeKeyID(), 
			'avID' => $av->getAttributeValueID()
		), array('cID', 'cvID', 'akID'));
	}
	
	public function add($akHandle, $akName, $akIsSearchable, $passthru, $atID, $akIsSearchableIndexed = false, $akIsAutoCreated = false, $akIsEditable = true, $pkg = false) {
		$ak = parent::add('collection', $akHandle, $akName, $akIsSearchable, $passthru, $akIsSearchableIndexed, $akIsAutoCreated, $akIsEditable, $atID, $pkg);
		return $ak;
	}
	
	public function delete() {
		parent::delete();
		$db = Loader::db();
		$r = $db->Execute('select avID from CollectionAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
		}
		$db->Execute('delete from CollectionAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		$db->Execute('delete from PageTypeAttributes where akID = ?', array($this->getAttributeKeyID()));
	}

}

class CollectionAttributeValue extends AttributeValue {

	public function setCollection($cObj) {
		$this->c = $cObj;
	}
	
	public static function getByID($avID) {
		$cav = new CollectionAttributeValue();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}

	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from CollectionAttributeValues where cID = ? and cvID = ? and akID = ? and avID = ?', array(
			$this->c->getCollectionID(), 
			$this->c->getVersionID(),
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));
	}
}