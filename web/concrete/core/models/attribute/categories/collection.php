<?
defined('C5_EXECUTE') or die("Access Denied.");
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
class Concrete5_Model_CollectionAttributeKey extends AttributeKey {

	public function getIndexedSearchTable() {
		return 'CollectionSearchIndexAttributes';
	}

	protected $searchIndexFieldDefinition = 'cID I(11) UNSIGNED NOTNULL DEFAULT 0 PRIMARY';

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
		return parent::getList('collection', array('akIsColumnHeader' => 1));	
	}
	public static function getSearchableIndexedList() {
		return parent::getList('collection', array('akIsSearchableIndexed' => 1));	
	}

	public static function getSearchableList() {
		return parent::getList('collection', array('akIsSearchable' => 1));	
	}

	public function getAttributeValue($avID, $method = 'getValue') {
		$av = CollectionAttributeValue::getByID($avID);
		if (is_object($av)) {
			$av->setAttributeKey($this);
			$value = $av->{$method}();
			$av->__destruct();
			unset($av);
			return $value;
		}
	}
	
	public static function getByID($akID) {
		$ak = new CollectionAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}

	public static function getByHandle($akHandle) {
		$ak = CacheLocal::getEntry('collection_attribute_key_by_handle', $akHandle);
		if (is_object($ak)) {
			return $ak;
		} else if ($ak == -1) {
			return false;
		}
		
		$ak = new CollectionAttributeKey();
		$ak->load($akHandle, 'akHandle');
		if ($ak->getAttributeKeyID() < 1) {
			$ak = -1;
		}

		CacheLocal::set('collection_attribute_key_by_handle', $akHandle, $ak);

		if ($ak === -1) {
			return false;
		}

		return $ak;
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
		unset($av);
	}
	
	public function add($at, $args, $pkg = false) {

		// legacy check
		$fargs = func_get_args();
		if (count($fargs) >= 5) {
			$at = $fargs[4];
			$pkg = false;
			$args = array('akHandle' => $fargs[0], 'akName' => $fargs[1], 'akIsSearchable' => $fargs[2]);
		}

		CacheLocal::delete('collection_attribute_key_by_handle', $args['akHandle']);

		$ak = parent::add('collection', $at, $args, $pkg);
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

class Concrete5_Model_CollectionAttributeValue extends AttributeValue {

	/**
	 * @param Collection $cObj
	 */
	public function setCollection($cObj) {
		$this->c = $cObj;
	}

	/**
	 * @return Collection
	 */
	public function getCollection() {
		return $this->c;
	}
	
	public static function getByID($avID) {
		$cav = new CollectionAttributeValue();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->c);
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CollectionAttributeValues where cID = ? and cvID = ? and akID = ? and avID = ?', array(
			$this->c->getCollectionID(), 
			$this->c->getVersionID(),
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));
		
		// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
		// Note: we're going to keep these around and not delete them. We'll just clean this up with an optimize job later on - this'll speed up page deletion by a ton.
		
		/*
		$num = $db->GetOne('select count(avID) from CollectionAttributeValues where avID = ?', array($this->getAttributeValueID()));
		if ($num < 1) {
			parent::delete();
		}
		*/


		
	}
}
